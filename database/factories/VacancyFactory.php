<?php

namespace Database\Factories;

use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacancyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vacancy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vacancy_name' => $this->faker-> jobTitle,
            'workers_amount' => $this->faker->numberBetween(1,10),
            'salary' =>$this->faker->numberBetween(3000,15000),
            'organization_id' =>$this->faker->numberBetween(1,10),
        ];
    }
}
