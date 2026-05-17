<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\MasterClass;
use App\Models\Registration;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_register_for_class_with_free_places()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();
        $mc = MasterClass::factory()->create([
            'user_id'          => $master->id,
            'category_id'      => $category->id,
            'max_participants' => 10,
        ]);

        $response = $this->actingAs($visitor)->post("/registration/{$mc->id}");
        $response->assertRedirect(route('category.show', $category->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('registrations', [
            'user_id'         => $visitor->id,
            'master_class_id' => $mc->id,
        ]);
    }

    public function test_visitor_cannot_register_twice_for_same_class()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $mc = MasterClass::factory()->create(['max_participants' => 10]);
        Registration::factory()->create([
            'user_id'         => $visitor->id,
            'master_class_id' => $mc->id,
        ]);

        $response = $this->actingAs($visitor)->post("/registration/{$mc->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(1, Registration::where('user_id', $visitor->id)->count());
    }

    public function test_visitor_cannot_register_when_no_free_places()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $mc = MasterClass::factory()->create(['max_participants' => 1]);
        Registration::factory()->create(['master_class_id' => $mc->id]);

        $response = $this->actingAs($visitor)->post("/registration/{$mc->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(1, Registration::count());
    }

    public function test_visitor_cannot_register_for_own_class()
    {
        $master = User::factory()->master()->create();
        $mc = MasterClass::factory()->create(['user_id' => $master->id]);
        $response = $this->actingAs($master)->post("/registration/{$mc->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_non_visitor_cannot_register()
    {
        $master = User::factory()->master()->create();
        $mc = MasterClass::factory()->create(['user_id' => User::factory()->master()->create()]);
        $response = $this->actingAs($master)->post("/registration/{$mc->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_confirmation_page_displays_correct_info()
    {
        $visitor = User::factory()->create(['role' => 'visitor', 'full_name' => 'Анна Смирнова']);
        $master = User::factory()->master()->create(['full_name' => 'Мастер Иванов']);
        $category = Category::factory()->create(['name' => 'Резьба по дереву']);
        $mc = MasterClass::factory()->create([
            'user_id'     => $master->id,
            'category_id' => $category->id,
            'date'        => '2026-12-10',
            'time'        => '09:00',
            'price'       => 1500,
        ]);

        $response = $this->actingAs($visitor)->get("/registration/{$mc->id}");
        $response->assertSee('Анна Смирнова');
        $response->assertSee('Резьба по дереву');
        $response->assertSee('Мастер Иванов');
        $response->assertSee('10.12.2026');
        $response->assertSee('09:00');
        $response->assertSee('1500');
    }
}
