<?php

namespace Database\Seeders;

use App\Models\Instrumento;
use Illuminate\Database\Seeder;

class InstrumentosBaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['PHQ9' => 'Cuestionario de Salud del Paciente (PHQ-9)', 'GAD7' => 'Escala de Ansiedad Generalizada (GAD-7)'] as $acronym => $name) {
            // Respetar identificadores, nombres personalizados y acrónimos en minúsculas.
            if (! Instrumento::whereRaw('LOWER(acronimo) = ?', [strtolower($acronym)])->exists()) {
                Instrumento::create(['acronimo' => $acronym, 'nombre' => $name]);
            }
        }
    }
}
