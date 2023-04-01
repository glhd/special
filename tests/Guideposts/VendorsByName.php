<?php

namespace Glhd\Guidepost\Tests\Guideposts;

use Glhd\Guidepost\Guidepost;
use Glhd\Guidepost\Tests\Models\Vendor;

enum VendorsByName: string
{
	use Guidepost;
	
	case BestBuy = 'Best Buy';
	
	case Amazon = 'Amazon';
	
	public function modelClass(): string
	{
		return Vendor::class;
	}
	
	protected function getKeyColumn(): string
	{
		return 'name';
	}
}
