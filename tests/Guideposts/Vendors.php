<?php

namespace Glhd\Guidepost\Tests\Guideposts;

use Glhd\Guidepost\Guidepost;
use Glhd\Guidepost\Tests\Models\Vendor;

// This is to test the class name inference
class_alias(Vendor::class, '\\App\\Models\\Vendor');

enum Vendors: string
{
	use Guidepost;
	
	case BestBuy = 'best-buy';
	
	case Amazon = 'amazon';
}
