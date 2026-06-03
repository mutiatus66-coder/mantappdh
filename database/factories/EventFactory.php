<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'nama_event' => 'Event ' . $this->faker->words(3, true),
            'jenis'      => $this->faker->randomElement(['INOTEK', 'INODA']),
        ];
    }
}