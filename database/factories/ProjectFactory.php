<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $end = (clone $start)->modify('+7 days');
        return [
            'name' => [
                'en' => $this->faker->catchPhrase . ' ' . $this->faker->year,
                'de' => $this->faker->catchPhrase . ' ' . $this->faker->year,
            ],
            'description' => [
                'en' => $this->faker->paragraph,
                'de' => $this->faker->paragraph,
            ],
            'location' => $this->faker->city,
            'language' => $this->faker->randomElement(['de', 'fr', 'it']),
            'start' => $start,
            'end' => $end,
            'allow_donation_until' => (clone $end)->modify('+7 days'),
            'image_landscape' => '/images/project' . $this->faker->numberBetween(1,3) . '.jpg',
            'image_square' => '/images/project' . $this->faker->numberBetween(1,3) . '-sq.jpg',
            'flat_rate_enabled' => $this->faker->boolean,
            'flat_rate_min_amount' => $this->faker->randomFloat(2, 10, 100),
            'flat_rate_help_text' => $this->faker->sentence,
            'unit_based_enabled' => $this->faker->boolean,
            'public_donation_enabled' => $this->faker->boolean,
            'created_by' => User::factory(),
        ];
    }
} 