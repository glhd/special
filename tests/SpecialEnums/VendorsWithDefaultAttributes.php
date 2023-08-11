<?php

namespace Glhd\Special\Tests\SpecialEnums;

use Glhd\Special\DefaultAttributes;
use Glhd\Special\EloquentBacking;
use Glhd\Special\Tests\Models\Vendor;

enum VendorsWithDefaultAttributes: string
{
	use EloquentBacking;
	
	#[DefaultAttributes(['name' => 'Best Buy'])]
	case BestBuy = 'best-buy';
	
	#[DefaultAttributes(['name' => 'Amazon.com'])]
	case Amazon = 'amazon';
	
	public function modelClass(): string
	{
		return Vendor::class;
	}
}
