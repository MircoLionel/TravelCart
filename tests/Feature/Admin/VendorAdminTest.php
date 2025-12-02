<?php

namespace Tests\Feature\Admin;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_vendor_and_audit_is_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'is_approved' => false,
            'legajo' => null,
        ]);

        $response = $this->from(route('admin.vendors.index'))
            ->actingAs($admin)
            ->patch(route('admin.vendors.update', $vendor), [
                'is_approved' => '1',
                'legajo' => 'VEN-42',
                'name' => 'Proveedor Demo',
                'email' => 'vendor@example.com',
            ]);

        $response->assertRedirect(route('admin.vendors.index'));
        $response->assertSessionHas('status');

        $vendor->refresh();
        $this->assertSame('vendor', $vendor->role);
        $this->assertTrue($vendor->is_approved);
        $this->assertFalse($vendor->is_admin);
        $this->assertSame('VEN-42', $vendor->legajo);
        $this->assertSame('Proveedor Demo', $vendor->name);
        $this->assertSame('vendor@example.com', $vendor->email);

        $audit = Audit::latest('id')->first();
        $this->assertNotNull($audit);
        $this->assertSame($admin->id, $audit->actor_id);
        $this->assertSame('vendor_updated', $audit->action);
        $this->assertSame(User::class, $audit->target_type);
        $this->assertSame($vendor->id, $audit->target_id);
        $this->assertSame('vendor', $audit->meta['role']);
        $this->assertTrue($audit->meta['is_approved']);
        $this->assertSame('VEN-42', $audit->meta['legajo']);
    }

    public function test_non_admin_cannot_access_vendor_admin(): void
    {
        $vendor = User::factory()->create(['role' => 'vendor']);

        $response = $this->actingAs($vendor)->get(route('admin.vendors.index'));

        $response->assertForbidden();
    }
}
