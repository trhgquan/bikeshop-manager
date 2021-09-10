<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class APITest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Resources using in this test.
     *
     * @var mixed
     */
    protected $order;
    protected $brand;
    protected $bike;
    protected $user;
    protected $month;

    /**
     * Setting up all testing resources.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->user = \App\Models\User::factory()->create();
        $this->brand = \App\Models\Brand::factory()->create();
        $this->bikes = \App\Models\Bike::factory()->count(10)->create();
        $this->order = \App\Models\Order::factory()->create();
        foreach ($this->bikes as $bike) {
            $this->order->bikes()->attach($bike->id, [
                'order_value'      => random_int(1, $bike->bike_stock),
                'order_buy_price'  => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price,
            ]);
        }

        $this->month = \Carbon\Carbon::now();
    }

    /**
     * Test if we cannot use API without API_TOKEN.
     *
     * @return void
     */
    public function test_api_without_key()
    {
        $this->json('GET', route('api.orders.month'), [
            'month' => \Carbon\Carbon::now(),
        ])
        ->assertStatus(401);

        $this->json('GET', route('api.report.month-revenue-stat'), [
            'month' => \Carbon\Carbon::now(),
        ])
        ->assertStatus(401);

        $this->json('GET', route('api.report.month-quantity-stat'), [
            'month' => \Carbon\Carbon::now(),
        ])
        ->assertStatus(401);

        $this->json('GET', route('api.orders.month'))->assertStatus(401);
        $this->json('GET', route('api.report.month-revenue-stat'))
            ->assertStatus(401);

        $this->json('GET', route('api.report.month-quantity-stat'))
            ->assertStatus(401);
    }

    /**
     * Test if we can return data correctly.
     *
     * @return void
     */
    public function test_quantity_api()
    {
        $this->json('GET', route('api.report.month-quantity-stat'), [
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertJsonValidationErrors('month', 'data.errors');

        $this->json('GET', route('api.report.month-quantity-stat'), [
            'month'     => $this->month,
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertExactJson(['data' => [
            'detail' => [],
            'items'  => 0,
            'month'  => $this->month->format('m-Y'),
        ]]);

        $this->order->checkout_at = $this->month;
        $this->order->save();

        $this->json('GET', route('api.report.month-quantity-stat'), [
            'month'     => $this->month,
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.items', $this->order->bikes->count());
    }

    /**
     * Test revenue API.
     *
     * @return void
     */
    public function test_revenue_api(): void
    {
        $this->json('GET', route('api.report.month-revenue-stat'), [
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertJsonValidationErrors('month', 'data.errors');

        $this->json('GET', route('api.report.month-revenue-stat'), [
            'month'     => $this->month,
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertExactJson(['data' => [
            'detail' => [],
            'items'  => 0,
            'month'  => $this->month->format('m-Y'),
            'total'  => [
                'quantity' => 0,
                'revenue'  => 0,
                'profit'   => 0,
            ],
        ]]);

        $this->order->checkout_at = $this->month;
        $this->order->save();

        $this->json('GET', route('api.report.month-revenue-stat'), [
            'month'     => $this->month,
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.items', $this->order->count())
        ->assertJsonPath('data.total.quantity', $this->order->quantity())
        ->assertJsonPath('data.total.revenue', $this->order->revenue())
        ->assertJsonPath('data.total.profit', $this->order->profit());
    }

    /**
     * Test Order Month API.
     *
     * @return void
     */
    public function test_order_month_api(): void
    {
        $this->json('GET', route('api.orders.month'), [
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertJsonValidationErrors('month', 'data.errors');

        $this->order = \App\Models\Order::factory()->count(10)->create();
        $random = $this->order->random();
        $random->checkout_at = $this->month;
        $random->save();

        $this->json('GET', route('api.orders.month'), [
            'month'     => $this->month,
            'api_token' => $this->user->api_token,
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.items', $this->order->count() + 1);
    }
}
