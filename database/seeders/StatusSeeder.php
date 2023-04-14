<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
            [
                'nom' => 'Nouveaux taches',
                'couleur' => 'rouge'
            ],
            [
                'nom' => 'En cours',
                'couleur' => 'bleu'
            ],
            [
                'nom' => 'Terminé',
                'couleur' => 'vert'
            ],
            [
                'nom' => 'En attente',
                'couleur' => 'jaune'
            ],
        ]);
    }
}
