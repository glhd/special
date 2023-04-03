<?php

namespace Glhd\Guidepost;

use Glhd\Guidepost\Exceptions\GuidepostModelNotFound;
use Glhd\Guidepost\Support\ValueHelper;
use Illuminate\Container\Container;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use RuntimeException;

trait Guidepost
{
	use ForwardsCalls;
	
	public function __call(string $name, array $arguments)
	{
		return $this->forwardCallTo($this->get(), $name, $arguments);
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
		$model = $this->model()->firstOrNew($this->attributes(), $this->values());
		
		if (! $model->exists && config('guidepost.fail_when_missing', true)) {
			throw new GuidepostModelNotFound($this);
		}
		
		$model->save();
		
		return $model;
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
		return Arr::except(ValueHelper::getValuesFor($this), [$this->getKeyColumn()]);
	}
	
	protected function getKeyColumn(): string
	{
		return is_int($this->value)
			? config('guidepost.default_int_key_name', 'id')
			: config('guidepost.default_string_key_name', 'slug');
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
		return sprintf('guidepost:%s:%s', $this->modelClass(), $this->value);
	}
}
