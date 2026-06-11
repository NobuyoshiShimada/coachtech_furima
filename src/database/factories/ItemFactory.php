<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'description' => 'これは商品の説明文です。',
            'price' => 1000,
            'image_url' => 'https://example.com',
            'status' => 'on_sale',
            'user_id' => User::factory(),
            'condition_id' => Condition::factory(),
            ];
    }
}
