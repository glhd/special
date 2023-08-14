<?php

namespace Glhd\Special\Support;

use Glhd\Special\Commands\ClearCache;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class SpecialServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->bootConfig();
		
		if ($this->app->runningInConsole()) {
			$this->commands([ClearCache::class]);
		}
		
		Builder::macro('special', fn($special_enum) => $special_enum->constrain($this));
		Builder::macro('hasSpecial', fn($special_enum) => $special_enum->constrain($this));
	}
	
	public function register()
	{
		$this->app->singleton(KeyMap::class, function(Container $app) {
			return new KeyMap(
				cache: $app->make(Repository::class),
				ttl: config('glhd-special.cache_ttl', 3600),
				limit: config('glhd-special.cache_limit', 50),
			);
		});
		
		$this->mergeConfigFrom($this->packageConfigFile(), 'glhd-special');
	}
	
	protected function bootConfig(): self
	{
		$this->publishes([
			$this->packageConfigFile() => $this->app->configPath('glhd-special.php'),
		], 'glhd-special-config');
		
		return $this;
	}
	
	protected function packageConfigFile(): string
	{
		return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'glhd-special.php';
	}
}
