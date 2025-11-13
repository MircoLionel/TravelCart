<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_unapproved_user_is_redirected_from_cart(): void
    {
        $user = User::factory()->unapproved()->create();

        $response = $this->actingAs($user)->get(route('cart.show'));

        $response->assertRedirect(route('account.pending'));
    }

    public function test_user_can_add_item_to_cart(): void
    {
        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 10,
            'available' => 10,
            'price' => 150000,
        ]);

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'qty' => 2,
        ]);

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('ok');

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);

        $item = CartItem::first();
        $this->assertNotNull($item);
        $this->assertSame($cart->id, $item->cart_id);
        $this->assertSame(2, $item->qty);
        $this->assertSame(150000, (int) $item->unit_price);
        $this->assertSame(300000, (int) $item->subtotal);
    }

    public function test_cannot_add_more_items_than_available(): void
    {
        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 2,
            'available' => 2,
            'price' => 120000,
        ]);

        $response = $this->from(route('cart.show'))
            ->actingAs($user)
            ->post(route('cart.add'), [
                'tour_id' => $tour->id,
                'tour_date_id' => $date->id,
                'qty' => 3,
            ]);

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_user_can_apply_valid_coupon(): void
    {
        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 10,
            'available' => 10,
            'price' => 100000,
        ]);

        $cart = Cart::forUserOpen($user);
        CartItem::create([
            'cart_id' => $cart->id,
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'qty' => 3,
            'unit_price' => 100000,
            'subtotal' => 300000,
        ]);

        $coupon = Coupon::create([
            'code' => 'AHORRA10',
            'type' => 'percent',
            'amount' => 10,
            'min_total' => 0,
            'role_only' => null,
            'max_uses' => null,
            'max_uses_per_user' => null,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        $response = $this->from(route('cart.show'))
            ->actingAs($user)
            ->post(route('cart.coupon.apply'), ['code' => 'ahorra10']);

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('ok');
        $this->assertSame($coupon->id, session('cart.coupon_id'));
        $this->assertSame('AHORRA10', session('cart.coupon_code'));
        $this->assertSame(30000, session('cart.discount'));
    }

    public function test_invalid_coupon_shows_error(): void
    {
        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 5,
            'available' => 5,
            'price' => 80000,
        ]);

        $cart = Cart::forUserOpen($user);
        CartItem::create([
            'cart_id' => $cart->id,
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'qty' => 1,
            'unit_price' => 80000,
            'subtotal' => 80000,
        ]);

        $response = $this->from(route('cart.show'))
            ->actingAs($user)
            ->post(route('cart.coupon.apply'), ['code' => 'invalido']);

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('error');
        $this->assertNull(session('cart.coupon_id'));
        $this->assertNull(session('cart.coupon_code'));
        $this->assertNull(session('cart.discount'));
    }

    public function test_user_can_remove_coupon(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession([
            'cart.coupon_id' => 99,
            'cart.coupon_code' => 'TEMP',
            'cart.discount' => 1234,
        ])->from(route('cart.show'))
            ->actingAs($user)
            ->delete(route('cart.coupon.remove'));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('ok');
        $this->assertNull(session('cart.coupon_id'));
        $this->assertNull(session('cart.coupon_code'));
        $this->assertNull(session('cart.discount'));
    }
}
