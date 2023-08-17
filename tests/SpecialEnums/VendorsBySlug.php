<?php

namespace Glhd\Special\Tests\SpecialEnums;

use Glhd\Special\EloquentBacking;
use Glhd\Special\Tests\Models\Vendor;

enum VendorsBySlug: string
{
	use EloquentBacking;
	
	case BestBuy = 'best-buy';
	
	case Amazon = 'amazon';
	
	public static function modelClass(): string
	{
		return Vendor::class;
	}
}
