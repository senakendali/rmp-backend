<?php

namespace Database\Factories;

use App\Models\Vendors;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorsFactory extends Factory
{
    protected $model = Vendors::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'goods_category' => [$this->faker->word, $this->faker->word],
            'pic_name' => $this->faker->name,
            'pic_phone' => $this->faker->phoneNumber,
            'pic_email' => $this->faker->email,
            'address' => $this->faker->address,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'verification_status' => $this->faker->randomElement(['verified', 'unverified']),
        ];
    }
}
