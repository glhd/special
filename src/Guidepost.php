<?php

namespace Glhd\Guidepost;

use Illuminate\Container\Container;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

trait Guidepost
{
	public function modelClass(): string
	{
		$basename = Str::of(static::class)->classBasename();
		$name = $basename->singular();
		
		foreach (['\\App', '\\App\\Models'] as $prefix) {
			if (class_exists($model_class = "{$prefix}\\{$name}")) {
				return $model_class;
			}
		}
		
		throw new RuntimeException("Unable to infer model name for '{$basename}' guidepost (tried to find a '{$name}' model but could not).");
	}
	
	/**
	 * Get a singleton instance of the matching model
	 */
	public function singleton(): Model
	{
		$container = Container::getInstance();
		
		$key = $this->cacheKey();
		
		if (! $container->has($key)) {
			$container->instance($key, $this->fresh());
		}
		
		return $container->get($key);
	}
	
	/**
	 * Clear the singleton instance from the container
	 */
	public function forgetSingleton(): void
	{
		Container::getInstance()->forgetInstance($this->cacheKey());
	}
	
	/**
	 * Get a copy of the matching model
	 */
	public function get(): Model
	{
		return clone $this->singleton();
	}
	
	/**
	 * Load a fresh copy of the matching model from the database
	 */
	public function fresh(): Model
	{
		$key = $this->cacheKey();
		
		if ($id = Cache::get($key)) {
			return $this->model()->find($id);
		}
		
		$fresh = $this->firstOrCreate();
		
		Cache::put($key, $fresh->getKey());
		
		return $fresh;
	}
	
	/**
	 * Load the model or create it for the first time
	 */
	public function firstOrCreate(): Model
	{
		return $this->model()->firstOrCreate($this->attributes(), $this->values());
	}
	
	/**
	 * Get the model's primary key
	 */
	public function getKey()
	{
		return $this->singleton()->getKey();
	}
	
	/**
	 * Apply foreign key constraints to a query builder
	 */
	public function constrain(Builder $query): Builder
	{
		return $query->where($this->model()->getForeignKey(), '=', $this->getKey());
	}
	
	protected function attributes(): array
	{
		return [
			$this->getKeyColumn() => $this->valueToAttribute($this->value),
		];
	}
	
	protected function values(): array
	{
		$values = [];
		
		if ($name = $this->getNameColumn()) {
			$values[$name] = $this->nameToAttribute($this->name);
		}
		
		// If we're running tests, let's use the factory to set up test data
		if (App::runningUnitTests() && $factory = $this->factory()) {
			$values = $factory->raw($values);
			
			// If we haven't configured a name column, we'll try a couple common attributes
			if (! $this->getNameColumn()) {
				foreach (['name', 'display_name', 'label', 'title', 'description'] as $attribute) {
					if (array_key_exists($attribute, $values)) {
						$values[$attribute] = $this->nameToAttribute($this->name);
					}
				}
			}
		}
		
		return $values;
	}
	
	protected function getKeyColumn(): string
	{
		return is_int($this->value)
			? config('guidepost.default_int_key_name', 'id')
			: config('guidepost.default_string_key_name', 'slug');
	}
	
	protected function getNameColumn(): ?string
	{
		return config('guidepost.default_name_column');
	}
	
	protected function nameToAttribute(string $name): mixed
	{
		return Str::headline($name);
	}
	
	protected function valueToAttribute($value): mixed
	{
		return $value;
	}
	
	protected function factory(): ?Factory
	{
		$class_name = $this->modelClass();
		
		if (method_exists($class_name, 'newFactory') && $factory = $class_name::newFactory()) {
			return $factory;
		}
		
		if (class_exists(Factory::resolveFactoryName($class_name))) {
			return Factory::factoryForModel($class_name);
		}
		
		return null;
	}
	
	protected function model(): Model
	{
		$class_name = $this->modelClass();
		
		return new $class_name();
	}
	
	protected function cacheKey(): string
	{
		return sprintf('guidepost:%s:%s', $this->modelClass(), $this->value);
	}
}
