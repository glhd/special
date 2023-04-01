<?php

namespace Glhd\Guidepost\Tests;

use Glhd\Guidepost\Support\GuidepostServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
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
				->prepend('\\Glhd\\Guidepost\\Tests\\Database\\Factories\\')
				->toString();
		});
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
