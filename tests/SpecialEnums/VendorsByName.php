<?php

namespace Glhd\Special\Tests\SpecialEnums;

use Glhd\Special\EloquentBacking;
use Glhd\Special\Tests\Models\Vendor;

enum VendorsByName: string
{
	use EloquentBacking;
	
	case BestBuy = 'Best Buy';
	
	case Amazon = 'Amazon';
	
	public static function modelClass(): string
	{
		return Vendor::class;
	}
	
	protected static function getKeyColumn(): string
	{
		return 'name';
	}
}
