<?php

namespace Glhd\Special\Tests\SpecialEnums;

use Glhd\Special\EloquentBacking;
use Glhd\Special\Tests\Models\Vendor;

// This is to test the class name inference
class_alias(Vendor::class, '\\App\\Models\\Vendor');

enum Vendors: string
{
	use EloquentBacking;
	
	case BestBuy = 'best-buy';
	
	case Amazon = 'amazon';
}
