<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * Attempts to create a new Order, expect to see the signature.
     * 
     * @return void
     */
    private function createAndAssert($formData, $signature) {
        $this->followingRedirects()
            ->from(route('orders.create'))
            ->post(route('orders.store'), $formData)
            ->assertStatus(200)
            ->assertSee($signature);
    }

    /**
     * Test if non-authenticated user cannot create new order.
     * 
     * @return void
     */
    public function test_create_order_without_authentication() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Create new user, brand and bikes.
        $user = \App\Models\User::factory()->create();

        $brand = \App\Models\Brand::factory()->create();

        $bikes = \App\Models\Bike::factory()
            ->count(random_int(1, 13))
            ->create();

        // Create and test order
        $order_detail = [];
        $quantity = 0;
        $revenue = 0;
        $profit = 0;
        foreach ($bikes as $bike) {
            $addValue = random_int(1, $bike->bike_stock);
            $quantity += $addValue;
            $revenue += $addValue * $bike->bike_sell_price;
            $profit += $addValue 
                * ($bike->bike_sell_price - $bike->bike_buy_price);
            array_push($order_detail, [
                'bike_id' => $bike->id,
                'order_value' => $addValue
            ]);
        }

        $formData = [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'order_detail' => $order_detail,
            '_token' => Session::token()
        ];

        $response = $this->from(route('orders.create'))
            ->post(route('orders.store'), $formData);

        $response->assertRedirect(route('auth.login.index'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_bike', 0);  
    }

    /**
     * Test if we can create Order with any User and Bikes.
     *
     * @return void
     */
    public function test_create_order_with_random_bikes() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Create new user, brand and bikes.
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::all()->random()->id,
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bikes = \App\Models\Bike::factory()
            ->count(random_int(1, 13))
            ->create();

        $this->actingAs($user);

        // Create and test order with random stock.
        $order_detail = [];
        $quantity = 0;
        $revenue = 0;
        $profit = 0;
        foreach ($bikes as $bike) {
            $addValue = random_int(1, $bike->bike_stock);
            $quantity += $addValue;
            $revenue += $addValue * $bike->bike_sell_price;
            $profit += $addValue 
                * ($bike->bike_sell_price - $bike->bike_buy_price);
            array_push($order_detail, [
                'bike_id' => $bike->id,
                'order_value' => $addValue
            ]);
        }

        $formData = [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'order_detail' => $order_detail,
            '_token' => Session::token()
        ];

        $this->followingRedirects()
            ->from(route('orders.create'))
            ->post(route('orders.store'), $formData)
            ->assertSee($quantity)
            ->assertSee($revenue)
            ->assertSee($profit);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_bike', $bikes->count());

        // Make sure that all calculations are correct.
        $order = \App\Models\Order::first();

        $this->assertEquals($order->quantity(), $quantity);
        $this->assertEquals($order->revenue(), $revenue);
        $this->assertEquals($order->profit(), $profit);

        // Make sure that all informations are correct.
        $response = $this->get(route('orders.show', $order))
            ->assertStatus(200);

        foreach ($order->bikes as $bike) {
            $response->assertSee($bike->pivot->order_value)
                ->assertSee(
                    $bike->pivot->order_sell_price
                )
                ->assertSee(
                    $bike->pivot->order_value * $bike->pivot->order_sell_price
                );
        }
    }

    /**
     * Test if invalid items cannot be used to create Order!
     * 
     * @return void
     */
    public function test_create_order_with_invalid_data() {
        Session::start();
        // $this->withoutExceptionHandling();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Create new user, brand and bikes.
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::all()->random()->id,
        ]);

        $this->actingAs($user);

        $brand = \App\Models\Brand::factory()->create();

        $bikes = \App\Models\Bike::factory()
            ->count(random_int(1, 13))
            ->create();
        $order_detail = [];

        // Create and test order with invalid order_value
        foreach ($bikes as $bike) {
            array_push($order_detail, [
                'bike_id' => $bike->id,
                'order_value' => $bike->bike_stock + 1
            ]);
        }
        $formData = [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'order_detail' => $order_detail,
            '_token' => Session::token()
        ];
        $this->createAndAssert($formData, 'Lỗi');

        // Invalid bike_id
        $formData['order_detail'] = [
            ['bike_id' => 1337, 'order_value' => 1],
            ['bike_id' => $bikes->first()->id, 'order_value' => 1],
        ];
        $this->createAndAssert($formData, 'Lỗi');

        // Missing bike_id.
        $formData['order_detail'] = [
            ['order_value' => 1337],
            ['bike_id' => $bikes->first()->id,'order_value' => 1]
        ];
        $this->createAndAssert($formData, 'Lỗi');

        // Missing order_value, but needs to have different bikes.
        $bike1 = $bikes->random()->id;
        $bike2 = $bikes->random()->id;
        while ($bike1 === $bike2) $bike2 = $bikes->random()->id;
        $formData['order_detail'] = [
            ['bike_id' => $bike1],
            ['bike_id' => $bike2, 'order_value' => 1],
        ];
        $this->createAndAssert($formData, 'Lỗi');
        
        // Ordering a bike twice
        $formData['order_detail'] = [
            ['bike_id' => $bike1, 'order_value' => 1],
            ['bike_id' => $bike1, 'order_value' => 2],
        ];
        $this->createAndAssert($formData, 'Lỗi');

        // Doesn't order anything!
        $formData['order_detail'] = [];
        $this->createAndAssert($formData, 'Lỗi');

        // At the final, no orders should be created!
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_bike', 0);
    }

    /**
     * Test if not everyone can delete a non-checkout order.
     * (as a user, we'll test if only that user can delete that order
     * while it's not checked out).
     * 
     * @return void
     */
    public function test_delete_non_checkout_order_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $this->actingAs($user);

        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order_detail = [];

        /**
         * Delete a non-checkedout order.
         * 
         * 
         */
        foreach ($bikes as $bike) {
            array_push($order_detail, [
                'bike_id' => $bike->id,
                'order_value' => 13
            ]);
        }

        $formData = [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'order_detail' => $order_detail,
            '_token' => Session::token()
        ];

        $response = $this->followingRedirects()
            ->from(route('orders.create'))
            ->post(route('orders.store', $formData))
            ->assertStatus(200);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_bike', $bikes->count());

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 1324);
        }

        $order = \App\Models\Order::first();

        $this->assertEquals($order->getCheckedOut(), false);

        $theOneCannotDelete = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $this->actingAs($theOneCannotDelete);

        $this->followingRedirects()
            ->from(route('orders.edit', $order))
            ->delete(route('orders.destroy', $order), [
                '_token' => Session::token()
            ])
            ->assertStatus(403);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'deleted_at' => NULL
        ]);
        
        $this->actingAs($user);
        $this->followingRedirects()
        ->from(route('orders.edit', $order))
        ->delete(route('orders.destroy', $order), [
            '_token' => Session::token()
        ])
        ->assertStatus(200);

        $this->assertSoftDeleted($order);
        
        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 1337);
        }

        $this->get(route('orders.show', $order))
            ->assertStatus(404);
    }

    /**
     * Although he did not created this order, Manager can still delete it.
     * 
     * @return void
     */
    public function test_delete_non_checkout_order_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $this->actingAs($user);

        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order_detail = [];

        /**
         * Delete a non-checkedout order.
         * 
         * 
         */
        foreach ($bikes as $bike) {
            array_push($order_detail, [
                'bike_id' => $bike->id,
                'order_value' => 13
            ]);
        }

        $formData = [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'order_detail' => $order_detail,
            '_token' => Session::token()
        ];

        $response = $this->followingRedirects()
            ->from(route('orders.create'))
            ->post(route('orders.store', $formData))
            ->assertStatus(200);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_bike', $bikes->count());

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 1324);
        }

        $order = \App\Models\Order::first();

        $this->assertEquals($order->getCheckedOut(), false);

        $theOneCanDeleteEveryOrder = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER,
        ]);

        $this->actingAs($theOneCanDeleteEveryOrder);
        $this->followingRedirects()
        ->from(route('orders.edit', $order))
        ->delete(route('orders.destroy', $order), [
            '_token' => Session::token()
        ])
        ->assertStatus(200);

        $this->assertSoftDeleted($order);
        
        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 1337);
        }

        $this->get(route('orders.show', $order))
            ->assertStatus(404);
    }

    /**
     * Test if staff cannot and manager can delete checked out orders.
     * 
     * @return void
     */
    public function test_delete_checkedout_order_with_both() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $this->actingAs($user);

        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order_detail = [];

        /**
         * Delete a non-checkedout order.
         * 
         * 
         */
        foreach ($bikes as $bike) {
            array_push($order_detail, [
                'bike_id' => $bike->id,
                'order_value' => 13
            ]);
        }

        $formData = [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'order_detail' => $order_detail,
            'order_checkout' => '1',
            '_token' => Session::token()
        ];

        $response = $this->followingRedirects()
            ->from(route('orders.create'))
            ->post(route('orders.store', $formData))
            ->assertStatus(200);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_bike', $bikes->count());

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 1324);
        }

        $order = \App\Models\Order::first();

        $this->assertEquals($order->getCheckedOut(), true);

        $this->followingRedirects()
            ->from(route('orders.edit', $order))
            ->delete(route('orders.destroy', $order), [
                '_token' => Session::token()
            ])
            ->assertStatus(403);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'deleted_at' => NULL
        ]);

        $theOneCanDelete = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER,
        ]);

        $this->actingAs($theOneCanDelete);
        $this->followingRedirects()
            ->from(route('orders.edit', $order))
            ->delete(route('orders.destroy', $order), [
                '_token' => Session::token()
            ])
            ->assertStatus(200)
            ->assertDontSee($order->customer_name)
            ->assertDontSee($order->customer_email);

        $this->assertSoftDeleted($order);

        $this->get(route('orders.show', $order))
            ->assertStatus(404);
    }
}
