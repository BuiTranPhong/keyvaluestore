<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = create(User::class);

        return [
            'key' => $this->faker->regexify('[A-Za-z0-9]{5,10}'),
            'value' => ['test' => 'value'],
            'timestamp' => now()->timestamp,
            'user_id' => $user->id,
        ];
    }
}
