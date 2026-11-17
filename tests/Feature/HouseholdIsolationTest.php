<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Household;
use App\Models\HouseholdUser;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseholdIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioConHogar(string $email, string $role = 'owner'): array
    {
        $user = User::factory()->create(['email' => $email]);
        $household = Household::create(['name' => 'Finanzas de '.$user->name]);

        HouseholdUser::create([
            'household_id' => $household->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);

        $user->useHousehold($household);

        return [$user, $household];
    }

    public function test_no_puede_editar_categoria_de_otro_hogar(): void
    {
        [$user] = $this->usuarioConHogar('uno@example.test');
        [, $ajeno] = $this->usuarioConHogar('dos@example.test');
        $category = $ajeno->categories()->create(['name' => 'Ajena', 'type' => 'gasto']);

        $response = $this->actingAs($user)->put("/categories/{$category->id}", [
            'name' => 'Robada',
            'type' => 'gasto',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Ajena']);
    }

    public function test_no_puede_crear_movimiento_con_categoria_ajena(): void
    {
        [$user] = $this->usuarioConHogar('uno@example.test');
        [, $ajeno] = $this->usuarioConHogar('dos@example.test');
        $category = $ajeno->categories()->create(['name' => 'Ajena', 'type' => 'gasto']);

        $response = $this->actingAs($user)->post('/transactions', [
            'category_id' => $category->id,
            'type' => 'gasto',
            'amount' => 1000,
            'date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_unirse_a_otro_hogar_conserva_el_anterior(): void
    {
        [$user, $propio] = $this->usuarioConHogar('uno@example.test');
        [, $ajeno] = $this->usuarioConHogar('dos@example.test');

        $this->actingAs($user)->post('/household/join', ['invite_code' => $ajeno->invite_code])
            ->assertRedirect(route('household.show'));

        $this->assertDatabaseHas('household_user', ['user_id' => $user->id, 'household_id' => $propio->id, 'role' => 'owner']);
        $this->assertDatabaseHas('household_user', ['user_id' => $user->id, 'household_id' => $ajeno->id, 'role' => 'viewer']);
        $this->assertEquals($ajeno->id, $user->fresh()->currentHousehold()->id);
    }

    public function test_borrar_cuenta_no_borra_movimientos_del_hogar(): void
    {
        [$owner, $hogar] = $this->usuarioConHogar('uno@example.test');
        $viewer = User::factory()->create(['email' => 'dos@example.test']);
        $hogar->users()->attach($viewer, ['role' => 'viewer']);
        $viewer->useHousehold($hogar);
        $category = $hogar->categories()->create(['name' => 'Comida', 'type' => 'gasto']);
        $movimiento = Transaction::create([
            'user_id' => $viewer->id,
            'category_id' => $category->id,
            'type' => 'gasto',
            'amount' => 5000,
            'amount_gs' => 5000,
            'currency' => 'PYG',
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($viewer)->delete('/profile', ['password' => 'password'])->assertRedirect('/');

        $this->assertDatabaseHas('transactions', ['id' => $movimiento->id, 'user_id' => null]);
        $this->assertEquals(1, $owner->fresh()->households()->count());
    }

    public function test_mes_fuera_de_rango_no_rompe_las_vistas(): void
    {
        [$user] = $this->usuarioConHogar('uno@example.test');

        $this->actingAs($user)->get('/transactions?month=99&year=1800')->assertOk();
        $this->actingAs($user)->get('/transactions/weekly?month=0&year=3000')->assertOk();
        $this->actingAs($user)->get('/summary/yearly?year=0')->assertOk();
    }

    public function test_no_puede_borrar_categoria_con_movimientos(): void
    {
        [$user, $hogar] = $this->usuarioConHogar('uno@example.test');
        $category = $hogar->categories()->create(['name' => 'Comida', 'type' => 'gasto']);
        $movimiento = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'gasto',
            'amount' => 5000,
            'amount_gs' => 5000,
            'currency' => 'PYG',
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($user)->delete("/categories/{$category->id}")
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('transactions', ['id' => $movimiento->id]);
    }

    public function test_no_puede_cambiar_tipo_de_categoria_con_movimientos(): void
    {
        [$user, $hogar] = $this->usuarioConHogar('uno@example.test');
        $category = $hogar->categories()->create(['name' => 'Comida', 'type' => 'gasto']);
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'gasto',
            'amount' => 5000,
            'amount_gs' => 5000,
            'currency' => 'PYG',
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($user)->put("/categories/{$category->id}", [
            'name' => 'Comida',
            'type' => 'ganancia',
        ])->assertSessionHasErrors('type');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'type' => 'gasto']);
    }

    public function test_no_puede_usar_categoria_de_ganancia_para_un_gasto(): void
    {
        [$user, $hogar] = $this->usuarioConHogar('uno@example.test');
        $category = $hogar->categories()->create(['name' => 'Sueldo', 'type' => 'ganancia']);

        $this->actingAs($user)->post('/transactions', [
            'category_id' => $category->id,
            'type' => 'gasto',
            'amount' => 1000,
            'date' => now()->toDateString(),
        ])->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_borrar_owner_promueve_a_otro_integrante(): void
    {
        [$owner, $hogar] = $this->usuarioConHogar('uno@example.test');
        $viewer = User::factory()->create(['email' => 'dos@example.test']);
        $hogar->users()->attach($viewer, ['role' => 'viewer']);

        $this->actingAs($owner)->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertDatabaseHas('household_user', [
            'user_id' => $viewer->id,
            'household_id' => $hogar->id,
            'role' => 'owner',
        ]);
    }

    public function test_puede_volver_a_hogar_anterior_y_no_a_uno_ajeno(): void
    {
        [$user, $propio] = $this->usuarioConHogar('uno@example.test');
        [, $ajeno] = $this->usuarioConHogar('dos@example.test');
        [, $tercero] = $this->usuarioConHogar('tres@example.test');

        $this->actingAs($user)->post('/household/join', ['invite_code' => $ajeno->invite_code]);
        $this->actingAs($user)->post('/household/switch', ['household_id' => $tercero->id])
            ->assertSessionHasErrors('household_id');

        $this->actingAs($user)->post('/household/switch', ['household_id' => $propio->id])
            ->assertRedirect(route('household.show'));

        $this->assertEquals($propio->id, $user->fresh()->currentHousehold()->id);
    }

    public function test_invitado_puede_salir_y_vuelve_a_su_hogar(): void
    {
        [$user, $propio] = $this->usuarioConHogar('uno@example.test');
        [, $ajeno] = $this->usuarioConHogar('dos@example.test');

        $this->actingAs($user)->post('/household/join', ['invite_code' => $ajeno->invite_code]);
        $this->actingAs($user)->delete('/household/leave', ['household_id' => $ajeno->id])
            ->assertRedirect(route('household.show'));

        $this->assertDatabaseMissing('household_user', ['user_id' => $user->id, 'household_id' => $ajeno->id]);
        $this->assertDatabaseHas('household_user', ['user_id' => $user->id, 'household_id' => $propio->id]);
        $this->assertEquals($propio->id, $user->fresh()->currentHousehold()->id);
    }

    public function test_owner_al_salir_promueve_a_otro_integrante(): void
    {
        [$owner, $hogar] = $this->usuarioConHogar('uno@example.test');
        $viewer = User::factory()->create(['email' => 'dos@example.test']);
        $hogar->users()->attach($viewer, ['role' => 'viewer']);
        $otro = Household::create(['name' => 'Otro hogar']);
        $owner->households()->attach($otro, ['role' => 'viewer']);

        $this->actingAs($owner)->delete('/household/leave', ['household_id' => $hogar->id])
            ->assertRedirect(route('household.show'));

        $this->assertDatabaseHas('household_user', [
            'user_id' => $viewer->id,
            'household_id' => $hogar->id,
            'role' => 'owner',
        ]);
    }

    public function test_no_puede_salir_del_unico_hogar(): void
    {
        [$user, $hogar] = $this->usuarioConHogar('uno@example.test');

        $this->actingAs($user)->delete('/household/leave', ['household_id' => $hogar->id])
            ->assertForbidden();

        $this->assertDatabaseHas('household_user', ['user_id' => $user->id, 'household_id' => $hogar->id]);
    }

    public function test_owner_puede_renombrar_y_viewer_no(): void
    {
        [$owner, $hogar] = $this->usuarioConHogar('uno@example.test');
        $viewer = User::factory()->create(['email' => 'dos@example.test']);
        $hogar->users()->attach($viewer, ['role' => 'viewer']);
        $viewer->useHousehold($hogar);

        $this->actingAs($owner)->patch('/household', ['name' => 'Casa nueva'])
            ->assertRedirect(route('household.show'));
        $this->assertDatabaseHas('households', ['id' => $hogar->id, 'name' => 'Casa nueva']);

        $this->actingAs($viewer)->patch('/household', ['name' => 'Robada'])->assertForbidden();
        $this->assertDatabaseHas('households', ['id' => $hogar->id, 'name' => 'Casa nueva']);
    }

    public function test_eliminar_hogar_reubica_integrantes_y_borra_categorias(): void
    {
        [$owner, $hogar] = $this->usuarioConHogar('uno@example.test');
        $viewer = User::factory()->create(['email' => 'dos@example.test']);
        $hogar->users()->attach($viewer, ['role' => 'viewer']);
        $viewer->useHousehold($hogar);
        $category = $hogar->categories()->create(['name' => 'Comida', 'type' => 'gasto']);

        $this->actingAs($owner)->delete('/household', ['confirm' => 1])
            ->assertRedirect(route('household.show'));

        $this->assertDatabaseMissing('households', ['id' => $hogar->id]);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('household_user', ['household_id' => $hogar->id]);
        $this->assertEquals(1, $owner->fresh()->households()->count());
        $this->assertEquals(1, $viewer->fresh()->households()->count());
        $this->assertNotNull($viewer->fresh()->currentHousehold());
    }

    public function test_viewer_no_puede_eliminar_hogar(): void
    {
        [$owner, $hogar] = $this->usuarioConHogar('uno@example.test');
        $viewer = User::factory()->create(['email' => 'dos@example.test']);
        $hogar->users()->attach($viewer, ['role' => 'viewer']);
        $viewer->useHousehold($hogar);

        $this->actingAs($viewer)->delete('/household', ['confirm' => 1])->assertForbidden();

        $this->assertDatabaseHas('households', ['id' => $hogar->id]);
    }
}
