<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_event' => fake()->sentence(3),
            'jenis_event' => fake()->randomElement([
                 'INOTEK',
                 'INODA',
               ]),
        ];
    }
}