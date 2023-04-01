<?php

namespace Glhd\Guidepost\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
	public function vendor()
	{
		return $this->belongsTo(Vendor::class);
	}
}
