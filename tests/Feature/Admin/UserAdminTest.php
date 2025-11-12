<?php

namespace Tests\Feature\Admin;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_user_and_audit_is_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'role' => 'buyer',
            'is_approved' => false,
        ]);

        $response = $this->from(route('admin.users.index'))
            ->actingAs($admin)
            ->patch(route('admin.users.update', $user), [
                'role' => 'vendor',
                'is_approved' => '1',
                'legajo' => 'VEN-42',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertSame('vendor', $user->role);
        $this->assertTrue($user->is_approved);
        $this->assertFalse($user->is_admin);
        $this->assertSame('VEN-42', $user->legajo);

        $audit = Audit::latest('id')->first();
        $this->assertNotNull($audit);
        $this->assertSame($admin->id, $audit->actor_id);
        $this->assertSame('user_updated', $audit->action);
        $this->assertSame(User::class, $audit->target_type);
        $this->assertSame($user->id, $audit->target_id);
        $this->assertSame('vendor', $audit->meta['role']);
        $this->assertTrue($audit->meta['is_approved']);
        $this->assertSame('VEN-42', $audit->meta['legajo']);
    }

    public function test_non_admin_cannot_access_admin_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertForbidden();
    }
}
