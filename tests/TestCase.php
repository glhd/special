<?php

namespace Glhd\Guidepost\Tests;

use Glhd\Guidepost\Support\GuidepostServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
	protected function setUp(): void
	{
		parent::setUp();
		
		Model::unguard();
	}
	
	protected function getPackageProviders($app)
	{
		return [
			GuidepostServiceProvider::class,
		];
	}
	
	protected function getPackageAliases($app)
	{
		return [];
	}
	
	protected function getApplicationTimezone($app)
	{
		return 'America/New_York';
	}
	
	protected function defineDatabaseMigrations()
	{
		$this->loadMigrationsFrom(__DIR__.'/database/migrations');
	}
}
