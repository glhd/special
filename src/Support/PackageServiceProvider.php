<?php

namespace Glhd\Guidepost\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
	public function boot()
	{
		// require_once __DIR__.'/helpers.php';
		// $this->bootConfig();
		// $this->bootViews();
		// $this->bootBladeComponents();
	}
	
	public function register()
	{
		$this->mergeConfigFrom($this->packageConfigFile(), 'guidepost');
	}
	
	protected function bootViews() : self
	{
		$this->loadViewsFrom($this->packageViewsDirectory(), 'guidepost');
		
		$this->publishes([
			$this->packageViewsDirectory() => $this->app->resourcePath('views/vendor/guidepost'),
		], 'guidepost-views');
		
		return $this;
	}
	
	protected function bootBladeComponents() : self
	{
		if (version_compare($this->app->version(), '8.0.0', '>=')) {
			Blade::componentNamespace('Glhd\\Guidepost\\Components', 'guidepost');
		}
		
		return $this;
	}
	
	protected function bootConfig() : self
	{
		$this->publishes([
			$this->packageConfigFile() => $this->app->configPath('guidepost.php'),
		], 'guidepost-config');
		
		return $this;
	}
	
	protected function packageConfigFile(): string
	{
		return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'config.php';
	}
	
	protected function packageTranslationsDirectory(): string
	{
		return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'translations';
	}
	
	protected function packageViewsDirectory(): string
	{
		return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views';
	}
}
