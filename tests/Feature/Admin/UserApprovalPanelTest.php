<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApprovalPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_registrations_surface_for_admin_review(): void
    {
        $admin = User::factory()->admin()->create();

        $newUser = User::factory()->create([
            'role'        => null,
            'is_approved' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response
            ->assertOk()
            ->assertSee('Pendientes por aprobar', false)
            ->assertSee((string) $newUser->id, false)
            ->assertSee($newUser->name, false)
            ->assertSee('Pendiente de aprobación', false)
            ->assertSee('Sin rol', false);
    }
}

