<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_user_role_to_master()
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['role' => 'visitor']);

        $response = $this->actingAs($admin)->patch("/admin/users/{$user->id}/role", [
            'role' => 'master',
        ]);

        $response->assertRedirect();
        $this->assertEquals('master', $user->fresh()->role);
    }

    public function test_admin_cannot_delete_himself()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_access_admin_panel()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $response = $this->actingAs($visitor)->get('/admin');
        $response->assertForbidden();
    }
}