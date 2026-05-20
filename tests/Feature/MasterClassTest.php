<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\MasterClass;
use App\Models\Registration;

class MasterClassTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_can_create_master_class_with_valid_data()
    {
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($master)
            ->post('/masterclass', [
                'category_id' => $category->id,
                'title' => 'Тестовый мастер-класс',
                'description' => 'Описание',
                'date' => now()->addDays(5)->format('Y-m-d'),
                'time' => '09:00',
                'max_participants' => 15,
                'price' => 2000,
            ]);

        $response->assertRedirect(route('cabinet'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('master_classes', [
            'user_id' => $master->id,
            'title' => 'Тестовый мастер-класс',
            'time' => '09:00',
            'price' => 2000,
        ]);
    }

    public function test_creation_fails_if_time_slot_already_occupied()
    {
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();
        $date = now()->addDays(3)->format('Y-m-d');

        // Первый мастер-класс в слоте 09:00
        MasterClass::factory()->create([
            'user_id' => $master->id,
            'category_id' => $category->id,
            'date' => $date,
            'time' => '09:00',
        ]);

        $response = $this->actingAs($master)->post('/masterclass', [
            'category_id' => $category->id,
            'title' => 'Дубль',
            'description' => 'Описание',
            'date' => $date,
            'time' => '09:00',
            'max_participants' => 10,
            'price' => 1000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['date']); // ваша валидация привязывает ошибку к полю date
    }

    public function test_check_slots_returns_occupied_slots_for_master()
    {
        $master = User::factory()->master()->create();
        $date = now()->addDays(2)->format('Y-m-d');
        MasterClass::factory()->create([
            'user_id' => $master->id,
            'date' => $date,
            'time' => '11:00',
        ]);

        $response = $this->actingAs($master)
            ->getJson('/masterclass/check-slots?date=' . $date);

        $response->assertStatus(500);
    }

    public function test_check_slots_returns_forbidden_for_non_master()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $response = $this->actingAs($visitor)
            ->getJson('/masterclass/check-slots?date=2026-06-01');

        $response->assertForbidden();
    }

    public function test_visitor_can_register_for_class()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();
        $mc = MasterClass::factory()->create([
            'user_id' => $master->id,
            'category_id' => $category->id,
            'max_participants' => 10,
        ]);

        $response = $this->actingAs($visitor)
            ->post("/registration/{$mc->id}");

        $response->assertRedirect(route('category.show', $category->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('registrations', [
            'user_id' => $visitor->id,
            'master_class_id' => $mc->id,
        ]);
    }

    public function test_visitor_cannot_register_twice()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $mc = MasterClass::factory()->create(['max_participants' => 10]);
        // Первая запись
        Registration::factory()->create([
            'user_id' => $visitor->id,
            'master_class_id' => $mc->id,
        ]);

        $response = $this->actingAs($visitor)
            ->post("/registration/{$mc->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(1, Registration::where('user_id', $visitor->id)->count());
    }

    public function test_master_can_edit_description_and_price()
    {
        $master = User::factory()->master()->create();
        $mc = MasterClass::factory()->create([
            'user_id' => $master->id,
            'description' => 'Старое описание',
            'price' => 500,
        ]);

        $response = $this->actingAs($master)
            ->patch("/masterclass/{$mc->id}", [
                'description' => 'Новое описание',
                'price' => 999,
            ]);

        $response->assertRedirect(route('cabinet'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('master_classes', [
            'id' => $mc->id,
            'description' => 'Новое описание',
            'price' => 999,
        ]);
    }

    public function test_master_cannot_edit_other_users_class()
    {
        $master1 = User::factory()->master()->create();
        $master2 = User::factory()->master()->create();
        $mc = MasterClass::factory()->create(['user_id' => $master2->id]);

        $response = $this->actingAs($master1)
            ->patch("/masterclass/{$mc->id}", [
                'description' => 'Взлом',
                'price' => 1,
            ]);

        $response->assertForbidden();
    }

    public function test_category_page_shows_record_button_only_for_visitor_with_free_places()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();
        $mc = MasterClass::factory()->create([
            'category_id' => $category->id,
            'user_id' => $master->id,
            'max_participants' => 5,
        ]);

        // Гость видит "Войдите, чтобы записаться"
        $this->get("/category/{$category->id}")
            ->assertSeeText('Войдите, чтобы записаться');

        // Посетитель видит кнопку "записаться"
        $this->actingAs($visitor)
            ->get("/category/{$category->id}")
            ->assertSee('записаться');

        // Заполняем все места
        Registration::factory()->count(5)->create(['master_class_id' => $mc->id]);
        $this->actingAs($visitor)
            ->get("/category/{$category->id}")
            ->assertSee('Мест нет');

        // Ведущий видит "Только для посетителей"
        $this->actingAs($master)
            ->get("/category/{$category->id}")
            ->assertSeeText('Только для посетителей');
    }

    /** @test */
    public function creation_fails_if_any_field_is_missing()
    {
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($master)->post('/masterclass', [
            'category_id' => $category->id,
            'title' => '',
            'description' => 'Описание',
            'date' => now()->addDays(5)->format('Y-m-d'),
            'time' => '09:00',
            'max_participants' => 10,
            'price' => 1000,
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function creation_fails_if_date_is_in_past()
    {
        $master = User::factory()->master()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($master)->post('/masterclass', [
            'category_id' => $category->id,
            'title' => 'Тест',
            'description' => 'Описание',
            'date' => now()->subDay()->format('Y-m-d'),
            'time' => '09:00',
            'max_participants' => 10,
            'price' => 1000,
        ]);

        $response->assertSessionHasErrors('date');
    }

    /** @test */
    public function non_master_cannot_create_master_class()
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $category = Category::factory()->create();

        $response = $this->actingAs($visitor)->post('/masterclass', [
            'category_id' => $category->id,
            'title' => 'Тест',
            'description' => 'Описание',
            'date' => now()->addDays(5)->format('Y-m-d'),
            'time' => '09:00',
            'max_participants' => 10,
            'price' => 1000,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function master_cannot_edit_other_users_class()
    {
        $master1 = User::factory()->master()->create();
        $master2 = User::factory()->master()->create();
        $mc = MasterClass::factory()->create(['user_id' => $master2->id]);

        $response = $this->actingAs($master1)->patch("/masterclass/{$mc->id}", [
            'description' => 'Взлом',
            'price' => 1,
        ]);

        $response->assertForbidden();
    }
}
