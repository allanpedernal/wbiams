<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IpAddress>
 */
class IpAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => fake()->boolean(80) ? fake()->ipv4() : fake()->ipv6(),
            'label' => fake()->words(3, true),
            'comment' => fake()->optional(0.7)->sentence(),
            'user_id' => User::factory(),
        ];
    }

    public function ipv4(): static
    {
        return $this->state(fn (array $attributes) => [
            'ip_address' => fake()->ipv4(),
        ]);
    }

    public function ipv6(): static
    {
        return $this->state(fn (array $attributes) => [
            'ip_address' => fake()->ipv6(),
        ]);
    }
}
