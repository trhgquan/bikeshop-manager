<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_order_with_random_bikes() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create();

        $brand = \App\Models\Brand::factory()->create();

        $bikes = \App\Models\Bike::factory()
            ->count(random_int(1, 13))
            ->create();

        Auth::login($user);

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
            ->from('orders.create')
            ->post(route('orders.store'), $formData)
            ->assertSee($quantity)
            ->assertSee($revenue)
            ->assertSee($profit);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_bike', $bikes->count());

        $order = \App\Models\Order::first();

        $this->assertEquals($order->quantity(), $quantity);
        $this->assertEquals($order->revenue(), $revenue);
        $this->assertEquals($order->profit(), $profit);

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
}
