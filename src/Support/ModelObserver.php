<?php

namespace Glhd\Special\Support;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use WeakMap;

class ModelObserver
{
	protected array $observed = [];

	public function __construct(
		protected KeyMap $key_map,
	) {
	}
	
	public function observe(Model $model, BackedEnum $enum): void
	{
		if (! isset($this->observed[$model::class])) {
			$this->registerModelObserver($model::class);
		}
		
		$this->registerEnumCallback($model, $enum);
	}
	
	protected function registerEnumCallback(Model $model, BackedEnum $enum): void
	{
		$this->observed[$model::class][$model->getKey()] ??= new WeakMap();
		$this->observed[$model::class][$model->getKey()]->offsetSet($enum, true);
	}
	
	/** @param class-string<Model> $class */
	protected function registerModelObserver(string $class): void
	{
		$class::updated($this->handleChange(...));
		$class::deleted($this->handleChange(...));
		
		$this->observed[$class] = [];
	}
	
	protected function handleChange(Model $model): void
	{
		if (! $map = $this->getMap($model)) {
			return;
		}
		
		// Clear the enums from the container for this request
		foreach ($map as $enum => $observed) {
			$enum->forgetSingleton();
		}
		
		// If the model was deleted, then also clear the key map, since
		// the ID is likely to change. This is not recommended, but we'll
		// handle the case to be safe.
		if (! $model->exists) {
			$this->key_map->clear();
		}
	}
	
	/** @return ?WeakMap<\Glhd\Special\EloquentBacking, bool> */
	protected function getMap(Model $model): ?WeakMap
	{
		return $this->observed[$model::class][$model->getKey()] ?? null;
	}
}
