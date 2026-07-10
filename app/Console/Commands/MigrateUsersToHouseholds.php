<?php

namespace App\Console\Commands;

use App\Models\Household;
use App\Models\User;
use Illuminate\Console\Command;

class MigrateUsersToHouseholds extends Command
{
    protected $signature = 'app:migrate-users-to-households';
    protected $description = 'Crea un household para cada usuario existente y le reasigna sus categorías';

    public function handle(): void
    {
        $usuarios = User::whereDoesntHave('households')->get();

        $this->info("Usuarios a migrar: {$usuarios->count()}");

        foreach ($usuarios as $user) {
            $household = Household::create([
                'name' => "Finanzas de {$user->name}",
            ]);

            $household->users()->attach($user->id, ['role' => 'owner']);

            $categoriasActualizadas = \DB::table('categories')
                ->where('user_id', $user->id)
                ->update(['household_id' => $household->id]);

            $this->line("- {$user->name}: household #{$household->id} creado, {$categoriasActualizadas} categorías reasignadas");
        }

        $this->info('¡Listo!');
    }
}