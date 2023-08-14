<?php

namespace Glhd\Special\Tests\SpecialEnums;

use Glhd\Special\CreateWith;
use Glhd\Special\EloquentBacking;
use Glhd\Special\Tests\Models\Vendor;

enum VendorsWithDefaultAttributes: string
{
	use EloquentBacking;
	
	#[CreateWith(['name' => 'Best Buy'])]
	case BestBuy = 'best-buy';
	
	#[CreateWith(['name' => 'Amazon.com'])]
	case Amazon = 'amazon';
	
	public function modelClass(): string
	{
		return Vendor::class;
	}
}
