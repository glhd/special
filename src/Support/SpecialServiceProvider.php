<?php

namespace Glhd\Special\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class SpecialServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->bootConfig();
		
		Builder::macro('special', fn($special_enum) => $special_enum->constrain($this));
		Builder::macro('hasSpecial', fn($special_enum) => $special_enum->constrain($this));
	}
	
	public function register()
	{
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
