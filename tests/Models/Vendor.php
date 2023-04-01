<?php

namespace Glhd\Guidepost\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
	public function prices()
	{
		return $this->hasMany(Price::class);
	}
}
