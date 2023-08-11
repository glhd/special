<?php

namespace Glhd\Special\Tests;

use Glhd\Special\Support\SpecialServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
	protected function setUp(): void
	{
		parent::setUp();
		
		Model::unguard();
		
		Factory::guessFactoryNamesUsing(function(string $model_class) {
			return Str::of($model_class)
				->classBasename()
				->append('Factory')
				->prepend('\\Glhd\\Special\\Tests\\Database\\Factories\\')
				->toString();
		});
		
		Config::set('glhd-special.fail_when_missing', false);
	}
	
	protected function getPackageProviders($app)
	{
		return [
			SpecialServiceProvider::class,
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
