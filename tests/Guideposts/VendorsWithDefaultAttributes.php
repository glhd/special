<?php

namespace Glhd\Guidepost\Tests\Guideposts;

use Glhd\Guidepost\DefaultAttributes;
use Glhd\Guidepost\Guidepost;
use Glhd\Guidepost\Tests\Models\Vendor;

enum VendorsWithDefaultAttributes: string
{
	use Guidepost;
	
	#[DefaultAttributes(['name' => 'Best Buy'])]
	case BestBuy = 'best-buy';
	
	#[DefaultAttributes(['name' => 'Amazon.com'])]
	case Amazon = 'amazon';
	
	public function modelClass(): string
	{
		return Vendor::class;
	}
}
