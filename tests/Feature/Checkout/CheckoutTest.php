<?php

namespace Tests\Feature\Checkout;

use App\Mail\OrderConfirmed;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_and_closes_cart(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 15,
            'available' => 15,
            'price' => 200000,
        ]);

        $cart = Cart::forUserOpen($user);
        CartItem::create([
            'cart_id' => $cart->id,
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'qty' => 2,
            'unit_price' => 200000,
            'subtotal' => 400000,
        ]);

        $coupon = Coupon::create([
            'code' => 'BONO500',
            'type' => 'fixed',
            'amount' => 50000,
            'min_total' => 0,
            'role_only' => null,
            'max_uses' => null,
            'max_uses_per_user' => null,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart.coupon_id' => $coupon->id,
                'cart.coupon_code' => $coupon->code,
                'cart.discount' => 50000,
            ])->post(route('checkout.place'));

        $order = Order::first();
        $this->assertNotNull($order);

        $response->assertRedirect(route('orders.show', $order));

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame(350000, (int) $order->total);
        $this->assertSame(50000, (int) $order->discount_total);
        $this->assertSame('BONO500', $order->applied_coupon_code);
        $this->assertSame('awaiting_passengers', $order->status);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'tour_id' => $tour->id,
            'qty' => 2,
        ]);

        $this->assertDatabaseHas('reservations', [
            'order_id' => $order->id,
            'status' => 'awaiting_passengers',
        ]);

        $this->assertDatabaseHas('coupon_redemptions', [
            'coupon_id' => $coupon->id,
            'order_id' => $order->id,
            'discount' => 50000,
        ]);

        $this->assertContains($cart->fresh()->status, ['closed', 'converted']);
        $this->assertNull(session('cart.coupon_id'));
        $this->assertNull(session('cart.coupon_code'));
        $this->assertNull(session('cart.discount'));

        Mail::assertSent(OrderConfirmed::class, function (OrderConfirmed $mail) use ($order, $user) {
            return $mail->hasTo($user->email) && $mail->order->is($order);
        });
    }

    public function test_checkout_requires_items_in_cart(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('checkout.place'));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_decrements_available_capacity(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 10,
            'available' => 10,
        ]);

        $cart = Cart::forUserOpen($user);
        CartItem::create([
            'cart_id' => $cart->id,
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'qty' => 4,
            'unit_price' => 150000,
            'subtotal' => 600000,
        ]);

        $this->actingAs($user)->post(route('checkout.place'));

        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $this->assertSame(6, $date->fresh()->available);
    }

    public function test_checkout_fails_when_not_enough_seats_available(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $tour = Tour::factory()->create(['title' => 'Experiencia Patagonia']);
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 5,
            'available' => 2,
        ]);

        $cart = Cart::forUserOpen($user);
        CartItem::create([
            'cart_id' => $cart->id,
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'qty' => 3,
            'unit_price' => 250000,
            'subtotal' => 750000,
        ]);

        $response = $this->actingAs($user)->post(route('checkout.place'));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('error', fn ($message) => str_contains($message, 'No hay suficientes cupos disponibles'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $date->fresh()->available);
    }
}
