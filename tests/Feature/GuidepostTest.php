<?php

namespace Glhd\Guidepost\Tests\Feature;

use Glhd\Guidepost\Guidepost;
use Glhd\Guidepost\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class GuidepostTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_core_functionality(): void
	{
		DB::enableQueryLog();
		
		$best_buy = Vendors::BestBuy->get();
		$amazon = Vendors::Amazon->get();
		
		// This will run 2 queries each, first check for existing record, and then create a new one
		$this->assertCount(4, DB::getQueryLog());
		
		// This shouldn't trigger any more queries
		Vendors::BestBuy->get();
		Vendors::BestBuy->singleton();
		Vendors::Amazon->get();
		Vendors::Amazon->singleton();
		$this->assertCount(4, DB::getQueryLog());
		
		// This should only trigger 2 more queries
		Vendors::BestBuy->fresh();
		Vendors::Amazon->fresh();
		$this->assertCount(6, DB::getQueryLog());
		
		$this->assertEquals('best-buy', $best_buy->slug);
		$this->assertEquals('amazon', $amazon->slug);
		$this->assertTrue($best_buy->exists);
		$this->assertTrue($amazon->exists);
	}
	
	public function test_relation_constraints(): void
	{
		$best_buy = Vendors::BestBuy->get();
		$amazon = Vendors::Amazon->get();
		
		$best_buy->prices()->create([
			'product' => 'PS4',
			'cents' => 400_00,
		]);
		
		$best_buy->prices()->create([
			'product' => 'PS5',
			'cents' => 500_00,
		]);
		
		$amazon->prices()->create([
			'product' => 'PS5',
			'cents' => 499_00,
		]);
		
		$prices = Price::query()
			->tap(Vendors::BestBuy->constrain(...))
			->get();
		
		$this->assertCount(2, $prices);
		$this->assertTrue($prices->where('vendor_id', Vendors::Amazon->getKey())->isEmpty());
	}
}

enum Vendors: string
{
	use Guidepost;
	
	case BestBuy = 'best-buy';
	
	case Amazon = 'amazon';
	
	public function modelClass(): string
	{
		return Vendor::class;
	}
}

class Vendor extends Model
{
	public function prices()
	{
		return $this->hasMany(Price::class);
	}
}

class Price extends Model
{
	public function vendor()
	{
		return $this->belongsTo(Vendor::class);
	}
}
