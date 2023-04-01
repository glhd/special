<?php

namespace Glhd\Guidepost\Tests\Guideposts;

use Glhd\Guidepost\Guidepost;
use Glhd\Guidepost\Tests\Models\Vendor;

enum VendorsBySlug: string
{
	use Guidepost;
	
	case BestBuy = 'best-buy';
	
	case Amazon = 'amazon';
	
	public function modelClass(): string
	{
		return Vendor::class;
	}
}
