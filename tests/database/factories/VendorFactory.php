<?php

namespace Glhd\Guidepost\Tests\Database\Factories;

use Glhd\Guidepost\Tests\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VendorFactory extends Factory
{
	protected $model = Vendor::class;
	
	public function definition(): array
	{
		return [
			'slug' => $this->faker->slug(),
			'name' => $this->faker->name(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		];
	}
}
