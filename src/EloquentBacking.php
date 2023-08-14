<?php

namespace Glhd\Special;

use Glhd\Special\Exceptions\BackingModelNotFound;
use Glhd\Special\Support\KeyMap;
use Glhd\Special\Support\ValueHelper;
use Illuminate\Container\Container;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use RuntimeException;

trait EloquentBacking
{
	use ForwardsCalls;
	
	public function __call(string $name, array $arguments)
	{
		return $this->forwardCallTo($this->singleton(), $name, $arguments);
	}
	
	public function modelClass(): string
	{
		$basename = Str::of(static::class)->classBasename();
		$name = $basename->singular();
		
		foreach (['\\App', '\\App\\Models'] as $prefix) {
			if (class_exists($model_class = "{$prefix}\\{$name}")) {
				return $model_class;
			}
		}
		
		throw new RuntimeException("Unable to infer model name for '{$basename}' special enum (tried to find a '{$name}' model but could not).");
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
		$attributes = $this->attributes();
		
		$builder = $this->model()->newQuery();
		
		if (! $model = $builder->where($attributes)->first()) {
			if (config('glhd-special.fail_when_missing', true)) {
				throw new BackingModelNotFound($this);
			}
			
			$model = $builder->create(array_merge($attributes, $this->values()));
		}
		
		return $model;
	}
	
	/**
	 * Get the model's primary key
	 */
	public function getKey()
	{
		return Container::getInstance()
			->make(KeyMap::class)
			->get($this);
	}
	
	/**
	 * Apply key/foreign key constraints to a query builder
	 */
	public function constrain(Builder $query): Builder
	{
		$key = $query->getModel()::class === $this->modelClass()
			? $this->model()->getKeyName()
			: $this->model()->getForeignKey();
		
		return $query->where($key, '=', $this->getKey());
	}
	
	protected function attributes(): array
	{
		return [
			$this->getKeyColumn() => $this->valueToAttribute($this->value),
		];
	}
	
	protected function values(): array
	{
		return Arr::except(ValueHelper::getValuesFor($this), [$this->getKeyColumn()]);
	}
	
	protected function getKeyColumn(): string
	{
		return is_int($this->value)
			? config('glhd-special.default_int_key_name', 'id')
			: config('glhd-special.default_string_key_name', 'slug');
	}
	
	protected function valueToAttribute($value): mixed
	{
		return $value;
	}
	
	protected function model(): Model
	{
		$class_name = $this->modelClass();
		
		return new $class_name();
	}
	
	protected function cacheKey(): string
	{
		return sprintf('glhd-special:%s:%s', $this->modelClass(), $this->value);
	}
}
