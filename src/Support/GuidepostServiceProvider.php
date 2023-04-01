<?php

namespace Glhd\Guidepost\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class GuidepostServiceProvider extends ServiceProvider
{
	public function boot()
	{
		// require_once __DIR__.'/helpers.php';
		
		$this->bootConfig();
		
		Builder::macro('forGuidepost', function($guidepost) {
			return $guidepost->constrain($this);
		});
	}
	
	public function register()
	{
		$this->mergeConfigFrom($this->packageConfigFile(), 'guidepost');
	}
	
	protected function bootConfig(): self
	{
		$this->publishes([
			$this->packageConfigFile() => $this->app->configPath('guidepost.php'),
		], 'guidepost-config');
		
		return $this;
	}
	
	protected function packageConfigFile(): string
	{
		return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'guidepost.php';
	}
}
