<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\MasterClass;
use App\Models\Registration;

class CabinetTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_can_see_only_own_master_classes_in_cabinet()
    {
        $master1 = User::factory()->master()->create();
        $master2 = User::factory()->master()->create();
        $mc1 = MasterClass::factory()->create(['user_id' => $master1->id, 'title' => 'МК мастера 1']);
        $mc2 = MasterClass::factory()->create(['user_id' => $master2->id, 'title' => 'МК мастера 2']);

        $response = $this->actingAs($master1)->get('/cabinet');
        $response->assertSee('МК мастера 1');
        $response->assertDontSee('МК мастера 2');
    }

    public function test_cabinet_shows_participants_list()
    {
        $master = User::factory()->master()->create();
        $visitor = User::factory()->create(['full_name' => 'Петр Петров']);
        $mc = MasterClass::factory()->create(['user_id' => $master->id]);
        Registration::factory()->create([
            'master_class_id' => $mc->id,
            'user_id'         => $visitor->id,
        ]);

        $response = $this->actingAs($master)->get('/cabinet');
        $response->assertSee('Петр Петров');
        $response->assertSee($visitor->email);
    }

    public function test_non_master_cannot_access_cabinet()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $response = $this->actingAs($visitor)->get('/cabinet');
        $response->assertForbidden();
    }
}
