<?php

namespace Tests\Feature;

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
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
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
            'order_detail' => $order_detail
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
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Create new user, brand and bikes.
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::all()->random()->id,
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bikes = \App\Models\Bike::factory()
            ->count(random_int(1, 13))
            ->create(['bike_stock' => 1337]);

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
            'order_detail' => $order_detail
        ];

        $this->followingRedirects()
            ->from(route('orders.create'))
            ->post(route('orders.store'), $formData)
            ->assertSee($quantity)
            ->assertSee($revenue)
            ->assertSee($profit);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_bike', $bikes->count());

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertNotEquals($bike->bike_stock, 1337);
        }

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
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
 
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Create new user, brand and bikes.
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::all()->random()->id,
        ]);

        $this->actingAs($user);

        $brand = \App\Models\Brand::factory()->create();

        $bikes = \App\Models\Bike::factory()
            ->count(random_int(10, 13))
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
            'order_detail' => $order_detail
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
     * Test if a user can delete his own order.
     */
    public function test_delete_non_checked_out_order_as_owner() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order = \App\Models\Order::factory()->create([
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id,
        ]);

        foreach ($bikes as $bike) {
            $order->bikes()->attach($bike->id, [
                'order_value' => $bike->bike_stock,
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ]);
            $bike->update(['bike_stock' => 0]);
        }

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_bike', $bikes->count());

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 0);
        }

        $order = \App\Models\Order::first();
        foreach ($order->bikes as $bike) {
            $this->assertEquals($bike->pivot->order_value, 1337);
        }

        // Attempts to delete.
        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('orders.show', $order))
            ->delete(route('orders.destroy', $order))
            ->assertStatus(200);
        
        $this->assertSoftDeleted($order);
        
        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 1337);
        }
    }

    /**
     * Test if a stranger cannot delete a user's order.
     * 
     * @return void
     */
    public function test_delete_non_checked_out_order_as_stranger() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);
        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order = \App\Models\Order::factory()->create([
            'created_by_user' => $user->id
        ]);

        foreach ($bikes as $bike) {
            $order->bikes()->attach($bike->id, [
                'order_value' => 1337,
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ]);
            $bike->update(['bike_stock' => 0]);
        }

        $this->actingAs($tester)
            ->followingRedirects()
            ->from(route('orders.show', $order))
            ->delete(route('orders.destroy', $order))
            ->assertStatus(403);

        $this->assertDatabaseHas($order, [
            'id' => $order->id,
            'checkout_at' => NULL,
        ]);

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertNotEquals($bike->bike_stock, 1337);
        }
    }

    /**
     * Test if Manager can delete order, even if he did not create it.
     * 
     * @return void
     */
    public function test_delete_non_checked_out_order_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);
        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER,
        ]);

        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order = \App\Models\Order::factory()->create([
            'created_by_user' => $user->id
        ]);

        foreach ($bikes as $bike) {
            $order->bikes()->attach($bike->id, [
                'order_value' => 1337,
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ]);
            $bike->update(['bike_stock' => 0]);
        }

        $this->actingAs($tester)
            ->followingRedirects()
            ->from(route('orders.show', $order))
            ->delete(route('orders.destroy', $order))
            ->assertStatus(200)
            ->assertDontSee($order->customer_name)
            ->assertDontSee($order->customer_email);

        $this->assertSoftDeleted($order);

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertEquals($bike->bike_stock, 1337);
        }
    }

    /**
     * Test if Staff cannot delete checked out order.
     * 
     * @return void
     */
    public function test_delete_checked_out_order_as_staff() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order = \App\Models\Order::factory()->create([
            'created_by_user' => $user->id,
            'checkout_at' => \Carbon\Carbon::now(),
        ]);

        foreach ($bikes as $bike) {
            $order->bikes()->attach($bike->id, [
                'order_value' => 1337,
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ]);
            $bike->update(['bike_stock' => 0]);
        }

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('orders.show', $order))
            ->delete(route('orders.destroy', $order))
            ->assertStatus(403);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'deleted_at' => NULL
        ]);

        $bikes = $bikes->fresh();
        foreach ($bikes as $bike) {
            $this->assertNotEquals($bike->bike_stock, 1337);
        }
    }

    /**
     * Test if Manager can delete checked out Order.
     * 
     * @return void
     */
    public function test_delete_checked_out_order_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);
        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN,
        ]);
        $brands = \App\Models\Brand::factory()->count(10)->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1337
        ]);

        $order = \App\Models\Order::factory()->create([
            'created_by_user' => $user->id,
            'checkout_at' => \Carbon\Carbon::now(),
        ]);

        foreach ($bikes as $bike) {
            $order->bikes()->attach($bike->id, [
                'order_value' => 1337,
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ]);
            $bike->update(['bike_stock' => 0]);
        }

        $this->actingAs($tester)
            ->followingRedirects()
            ->from(route('orders.show', $order))
            ->delete(route('orders.destroy', $order))
            ->assertStatus(200)
            ->assertDontSee($order->customer_name)
            ->assertDontSee($order->customer_email);
        
        $this->assertSoftDeleted($order);
    }

    /**
     * Test if we can view Order Update with all bikes.
     * 
     * @return void
     */
    public function test_view_update_order() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brand = \App\Models\Brand::factory()->create();
        $bikes = \App\Models\Bike::factory()->count(10)->create([
            'bike_stock' => 1330
        ]);
        $bike = \App\Models\Bike::all()->random();

        $order = \App\Models\Order::factory()->create();
        $order->bikes()->attach($bike, [
            'order_value' => 7,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $response = $this->actingAs($tester)
            ->get(route('orders.edit', $order));

        foreach ($bikes as $bike) {
            $response->assertSee($bike->bike_name);
        }
    }

    /**
     * Test if Owner can update his Order.
     * 
     * @return void
     */
    public function test_update_order_as_owner() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brand = \App\Models\Brand::factory()->create();
        $bike = \App\Models\Bike::factory()->create([
            'bike_stock' => 1330
        ]);

        $order = \App\Models\Order::factory()->create();
        $order->bikes()->attach($bike, [
            'order_value' => 7,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('orders.edit', $order))
            ->put(route('orders.update', $order), [
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'order_detail' => [
                    ['bike_id' => $bike->id, 'order_value' => 1337]
                ]
            ])
            ->assertSee(1337);

        $bike = $bike->fresh();
        $this->assertEquals($bike->bike_stock, 0);
    }

    /**
     * Test if Stranger cannot update orders he did not created.
     * 
     * @return void
     */
    public function test_update_order_as_stranger() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brand = \App\Models\Brand::factory()->create();
        $bike = \App\Models\Bike::factory()->create([
            'bike_stock' => 1330
        ]);

        $order = \App\Models\Order::factory()->create();
        $order->bikes()->attach($bike, [
            'order_value' => 7,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $this->actingAs($tester)
            ->from(route('orders.edit', $order))
            ->put(route('orders.update', $order), [
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'order_detail' => [
                    ['bike_id' => $bike->id, 'order_value' => 1337]
                ]
            ])
            ->assertStatus(403);

        $bike = $bike->fresh();
        $this->assertNotEquals($bike->bike_stock, 0);
    }

    /**
     * Test if Manager can update Order he did not created.
     * 
     * @return void
     */
    public function test_update_order_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brand = \App\Models\Brand::factory()->create();
        $bike = \App\Models\Bike::factory()->create([
            'bike_stock' => 1330
        ]);

        $order = \App\Models\Order::factory()->create();
        $order->bikes()->attach($bike, [
            'order_value' => 7,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER,
        ]);

        $this->actingAs($tester)
            ->followingRedirects()
            ->from(route('orders.edit', $order))
            ->put(route('orders.update', $order), [
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'order_detail' => [
                    ['bike_id' => $bike->id, 'order_value' => 1337]
                ]
            ])
            ->assertSee(1337);

        $bike = $bike->fresh();
        $this->assertEquals($bike->bike_stock, 0);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertSee(1337)
            ->assertSee($tester->nameAndUsername());
    }

    /**
     * Test if we cannot update Order with non exist bikes.
     * 
     * @return void
     */
    public function test_update_order_with_not_exist_bikes() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brand = \App\Models\Brand::factory()->create();
        $bike = \App\Models\Bike::factory()->create([
            'bike_stock' => 1330
        ]);

        $order = \App\Models\Order::factory()->create();
        $order->bikes()->attach($bike, [
            'order_value' => 7,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $this->actingAs($tester)
            ->from(route('orders.edit', $order))
            ->put(route('orders.update', $order), [
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'order_detail' => [
                    ['bike_id' => 1337, 'order_value' => 1337]
                ]
            ])
            ->assertSessionHasErrors(['order_detail.*.bike_id']);

        $bike = $bike->fresh();
        $this->assertEquals($bike->bike_stock, 1330);
    }

    /**
     * Test if we cannot update Order with overstock.
     * 
     * @return void
     */
    public function test_update_order_with_overstock() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $tester = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $brand = \App\Models\Brand::factory()->create();
        $bike = \App\Models\Bike::factory()->create([
            'bike_stock' => 1330
        ]);

        $order = \App\Models\Order::factory()->create();
        $order->bikes()->attach($bike, [
            'order_value' => 7,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $response = $this->actingAs($tester)
            ->from(route('orders.edit', $order))
            ->put(route('orders.update', $order), [
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'order_detail' => [
                    ['bike_id' => $bike->id, 'order_value' => 7331]
                ]
            ])
            ->assertSessionHasErrors();

        $bike = $bike->fresh();
        $this->assertEquals($bike->bike_stock, 1330);
    }

    /**
     * Test if no one can update checked out Orders.
     * 
     * @return void
     */
    public function test_update_checkedout_orders() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();
        $bike = \App\Models\Bike::factory()->create();

        $order = \App\Models\Order::factory()->create([
            'checkout_at' => \Carbon\Carbon::now()
        ]);
        $order->bikes()->attach($bike->id, [
            'order_value' => 1337,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price
        ]);

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('orders.show', $order))
            ->put(route('orders.update', $order), [
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'order_detail' => [
                    ['bike_id' => $bike->id, 'order_value' => 1]
                ]
            ])
            ->assertStatus(403);
        
        $this->assertEquals($order->quantity(), 1337);
    }
}
