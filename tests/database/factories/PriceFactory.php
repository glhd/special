<?php

namespace Glhd\Special\Tests\Database\Factories;

use Glhd\Special\Tests\Models\Price;
use Glhd\Special\Tests\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PriceFactory extends Factory
{
	protected $model = Price::class;
	
	public function definition(): array
	{
		return [
			'product' => $this->faker->word(),
			'cents' => $this->faker->randomNumber(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
			
			'vendor_id' => Vendor::factory(),
		];
	}
}
