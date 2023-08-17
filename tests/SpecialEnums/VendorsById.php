<?php

namespace Glhd\Special\Tests\SpecialEnums;

use Glhd\Special\EloquentBacking;
use Glhd\Special\Tests\Models\Vendor;

enum VendorsById: int
{
	use EloquentBacking;
	
	case BestBuy = 99;
	
	case Amazon = 199;
	
	public static function modelClass(): string
	{
		return Vendor::class;
	}
}
