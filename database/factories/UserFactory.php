<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\InstStatusCode;
use App\Enums\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Spatie\Tags\Tag;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'country' => $this->faker->country(),
            'birth_year' => $this->faker->year(),
            'city' => $this->faker->city(),
            'gender' => $this->getRandomGender(),
            // 'home_institution'=>'W&H (Austria)',
            'institution_status_code' => $this->getRandomHILegalStatusCountryCode(),
            'institution_city' => $this->faker->city(),
            'institution_address' => $this->faker->address(),
            'job' => $this->faker->jobTitle(),
            'position' => $this->getRandomPosition(),
            // 'mailing_address'=>$this->faker->address(),
            'mobile_phone' => $this->faker->phoneNumber(),
            'office_phone' => $this->faker->phoneNumber(),
            'academic_background' => $this->faker->paragraph(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'short_cv' => $this->faker->paragraph(),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn(array $attributes, User $user) => [
                    'name' => $user->name . '\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                    'complete_profile' => false
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }

    public function getRandomGender()
    {
        $cases = Gender::cases();
        $randomCase = $cases[array_rand($cases)];
        return $randomCase->name;
    }

    public static function getRandomHILegalStatusCountryCode()
    {
        $cases = InstStatusCode::cases();
        $randomCase = $cases[array_rand($cases)];
        return $randomCase->name;
    }

    public static function getRandomPosition()
    {
        $cases = Position::cases();
        $randomCase = $cases[array_rand($cases)];
        return $randomCase->name;
    }
}
