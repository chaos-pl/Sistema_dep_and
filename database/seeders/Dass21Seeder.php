<?php

namespace Database\Seeders;

use App\Models\Dass21Question;
use App\Models\Instrumento;
use Illuminate\Database\Seeder;

class Dass21Seeder extends Seeder
{
    public function run(): void
    {
        /*
        |----------------------------------------------------------------------
        | 1. Registrar el instrumento DASS-21
        |----------------------------------------------------------------------
        */
        $instrumento = Instrumento::firstOrCreate(
            ['acronimo' => 'DASS21'],
            ['nombre' => 'DASS-21 (Escalas de Depresión, Ansiedad y Estrés)']
        );

        /*
        |----------------------------------------------------------------------
        | 2. Los 21 reactivos oficiales en español
        |----------------------------------------------------------------------
        | Dimensiones:
        |   Estrés (S):    ítems 1, 6, 8, 11, 12, 14, 18
        |   Ansiedad (A):  ítems 2, 4, 7, 9, 15, 19, 20
        |   Depresión (D): ítems 3, 5, 10, 13, 16, 17, 21
        |----------------------------------------------------------------------
        */
        $items = [
            // --- Estrés ---
            ['item_number' => 1,  'dimension' => 'stress',     'statement' => 'Me costó mucho relajarme'],
            // --- Ansiedad ---
            ['item_number' => 2,  'dimension' => 'anxiety',    'statement' => 'Me di cuenta de que tenía la boca seca'],
            // --- Depresión ---
            ['item_number' => 3,  'dimension' => 'depression', 'statement' => 'No podía sentir ningún sentimiento positivo'],
            // --- Ansiedad ---
            ['item_number' => 4,  'dimension' => 'anxiety',    'statement' => 'Se me hizo difícil respirar (por ejemplo, respiración excesivamente rápida, falta de aliento sin haber hecho esfuerzo físico)'],
            // --- Depresión ---
            ['item_number' => 5,  'dimension' => 'depression', 'statement' => 'Se me hizo difícil tomar la iniciativa para hacer cosas'],
            // --- Estrés ---
            ['item_number' => 6,  'dimension' => 'stress',     'statement' => 'Reaccioné exageradamente en ciertas situaciones'],
            // --- Ansiedad ---
            ['item_number' => 7,  'dimension' => 'anxiety',    'statement' => 'Sentí que mis manos temblaban'],
            // --- Estrés ---
            ['item_number' => 8,  'dimension' => 'stress',     'statement' => 'Sentí que estaba muy nervioso/a'],
            // --- Ansiedad ---
            ['item_number' => 9,  'dimension' => 'anxiety',    'statement' => 'Estuve preocupado/a por situaciones en las cuales podía tener pánico o en las que podría hacer el ridículo'],
            // --- Depresión ---
            ['item_number' => 10, 'dimension' => 'depression', 'statement' => 'Sentí que no tenía nada por qué vivir'],
            // --- Estrés ---
            ['item_number' => 11, 'dimension' => 'stress',     'statement' => 'Noté que me agitaba'],
            // --- Estrés ---
            ['item_number' => 12, 'dimension' => 'stress',     'statement' => 'Se me hizo difícil relajarme'],
            // --- Depresión ---
            ['item_number' => 13, 'dimension' => 'depression', 'statement' => 'Me sentí triste y deprimido/a'],
            // --- Estrés ---
            ['item_number' => 14, 'dimension' => 'stress',     'statement' => 'No toleré nada que me impidiera seguir con lo que estaba haciendo'],
            // --- Ansiedad ---
            ['item_number' => 15, 'dimension' => 'anxiety',    'statement' => 'Sentí que estaba al punto de pánico'],
            // --- Depresión ---
            ['item_number' => 16, 'dimension' => 'depression', 'statement' => 'No me pude entusiasmar por nada'],
            // --- Depresión ---
            ['item_number' => 17, 'dimension' => 'depression', 'statement' => 'Sentí que valía muy poco como persona'],
            // --- Estrés ---
            ['item_number' => 18, 'dimension' => 'stress',     'statement' => 'Sentí que estaba muy irritable'],
            // --- Ansiedad ---
            ['item_number' => 19, 'dimension' => 'anxiety',    'statement' => 'Sentí los latidos de mi corazón a pesar de no haber hecho ningún esfuerzo físico (por ejemplo, sentí que el corazón se me aceleraba o que perdía un latido)'],
            // --- Ansiedad ---
            ['item_number' => 20, 'dimension' => 'anxiety',    'statement' => 'Tuve miedo sin razón'],
            // --- Depresión ---
            ['item_number' => 21, 'dimension' => 'depression', 'statement' => 'Sentí que la vida no tenía ningún sentido'],
        ];

        foreach ($items as $item) {
            Dass21Question::firstOrCreate(
                [
                    'instrument_id' => $instrumento->id,
                    'item_number'   => $item['item_number'],
                ],
                [
                    'statement' => $item['statement'],
                    'dimension' => $item['dimension'],
                ]
            );
        }
    }
}
