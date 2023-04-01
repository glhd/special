<?php

namespace Glhd\Guidepost\Tests\Guideposts;

use Glhd\Guidepost\Guidepost;
use Glhd\Guidepost\Tests\Models\Vendor;

enum VendorsById: int
{
	use Guidepost;
	
	case BestBuy = 99;
	
	case Amazon = 199;
	
	public function modelClass(): string
	{
		return Vendor::class;
	}
}
