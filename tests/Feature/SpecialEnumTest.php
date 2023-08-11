<?php

namespace Glhd\Special\Tests\Feature;

use Glhd\Special\Exceptions\BackingModelNotFound;
use Glhd\Special\Tests\SpecialEnums\Vendors;
use Glhd\Special\Tests\SpecialEnums\VendorsById;
use Glhd\Special\Tests\SpecialEnums\VendorsByName;
use Glhd\Special\Tests\SpecialEnums\VendorsBySlug;
use Glhd\Special\Tests\SpecialEnums\VendorsWithDefaultAttributes;
use Glhd\Special\Tests\Models\Price;
use Glhd\Special\Tests\Models\Vendor;
use Glhd\Special\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SpecialEnumTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_core_functionality(): void
	{
		DB::enableQueryLog();
		
		$best_buy = VendorsBySlug::BestBuy->get();
		$amazon = VendorsBySlug::Amazon->get();
		
		// This will run 2 queries each, first check for existing record, and then create a new one
		$this->assertCount(4, DB::getQueryLog());
		
		// This shouldn't trigger any more queries
		VendorsBySlug::BestBuy->get();
		VendorsBySlug::BestBuy->singleton();
		VendorsBySlug::Amazon->get();
		VendorsBySlug::Amazon->singleton();
		$this->assertCount(4, DB::getQueryLog());
		
		// This should only trigger 2 more queries
		VendorsBySlug::BestBuy->fresh();
		VendorsBySlug::Amazon->fresh();
		$this->assertCount(6, DB::getQueryLog());
		
		$this->assertEquals('best-buy', $best_buy->slug);
		$this->assertEquals('amazon', $amazon->slug);
		$this->assertTrue($best_buy->exists);
		$this->assertTrue($amazon->exists);
	}
	
	public function test_relation_constraints(): void
	{
		$best_buy = VendorsBySlug::BestBuy->get();
		$amazon = VendorsBySlug::Amazon->get();
		
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
			->tap(VendorsBySlug::BestBuy->constrain(...))
			->get();
		
		$this->assertCount(2, $prices);
		$this->assertTrue($prices->where('vendor_id', VendorsBySlug::Amazon->getKey())->isEmpty());
		
		$prices = Price::query()
			->special(VendorsBySlug::BestBuy)
			->get();
		
		$this->assertCount(2, $prices);
		$this->assertTrue($prices->where('vendor_id', VendorsBySlug::Amazon->getKey())->isEmpty());
	}
	
	public function test_model_name_inference(): void
	{
		$vendor = Vendors::BestBuy->get();
		
		$this->assertInstanceOf(Vendor::class, $vendor);
	}
	
	public function test_id_based_enums(): void
	{
		$this->assertEquals(99, VendorsById::BestBuy->getKey());
		$this->assertEquals(199, VendorsById::Amazon->getKey());
	}
	
	public function test_custom_key_column(): void
	{
		$this->assertEquals('Best Buy', VendorsByName::BestBuy->get()->name);
		$this->assertEquals('Amazon', VendorsByName::Amazon->get()->name);
	}
	
	public function test_it_uses_cache(): void
	{
		DB::enableQueryLog();
		
		// Initial request should take two queries
		$best_buy = VendorsBySlug::BestBuy->get();
		$this->assertCount(2, DB::getQueryLog());
		
		// Forget our locally saved singleton (emulate a new request)
		VendorsBySlug::BestBuy->forgetSingleton();
		
		// Subsequent requests should only take one
		$best_buy = VendorsBySlug::BestBuy->get();
		$this->assertCount(3, DB::getQueryLog());
	}
	
	public function test_it_uses_factories(): void
	{
		$best_buy = VendorsBySlug::BestBuy->get();
		
		$this->assertEquals('Best Buy', $best_buy->name);
		$this->assertNotEmpty($best_buy->slug);
	}
	
	public function test_attribute_annotations(): void
	{
		$this->assertEquals('Amazon.com', VendorsWithDefaultAttributes::Amazon->get()->name);
	}
	
	public function test_fail_when_missing_triggers_exception_when_missing(): void
	{
		Config::set('glhd-special.fail_when_missing', true);
		
		$this->expectException(BackingModelNotFound::class);
		
		VendorsBySlug::BestBuy->get();
	}
	
	public function test_fail_when_missing_does_not_trigger_exception_when_present(): void
	{
		Config::set('glhd-special.fail_when_missing', true);
		
		Vendor::factory(['slug' => 'best-buy'])->create();
		
		$vendor = VendorsBySlug::BestBuy->get();
		
		$this->assertEquals('best-buy', $vendor->slug);
	}
}
