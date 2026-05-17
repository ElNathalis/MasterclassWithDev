<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\MasterClass;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_categories_and_company_info()
    {
        $category = Category::factory()->create(['name' => 'Кулинария']);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Кулинария');
        $response->assertSee('ОчУмелые ручки');
    }

    public function test_category_page_shows_master_classes()
    {
        $category = Category::factory()->create();
        $master = User::factory()->master()->create();
        $mc = MasterClass::factory()->create([
            'category_id' => $category->id,
            'user_id'     => $master->id,
            'title'       => 'Валяние из шерсти',
        ]);

        $response = $this->get("/category/{$category->id}");
        $response->assertSee('Валяние из шерсти');
        $response->assertSee('записаться');
    }

    public function test_guest_sees_login_link_instead_of_record_button()
    {
        $category = Category::factory()->create();
        MasterClass::factory()->create(['category_id' => $category->id]);

        $response = $this->get("/category/{$category->id}");
        $response->assertSeeText('Войдите, чтобы записаться');
    }

    public function test_authenticated_visitor_sees_record_button()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $category = Category::factory()->create();
        MasterClass::factory()->create([
            'category_id'      => $category->id,
            'max_participants' => 10,
        ]);

        $response = $this->actingAs($visitor)->get("/category/{$category->id}");
        $response->assertSee('записаться');
    }

    public function test_master_sees_message_for_non_visitors()
    {
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();
        MasterClass::factory()->create([
            'category_id' => $category->id,
            'user_id'     => $master->id,
        ]);

        $response = $this->actingAs($master)->get("/category/{$category->id}");
        $response->assertSeeText('Только для посетителей');
    }
}
