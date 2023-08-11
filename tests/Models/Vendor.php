<?php

namespace Glhd\Special\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
	use HasFactory;
	
	public function prices()
	{
		return $this->hasMany(Price::class);
	}
}
