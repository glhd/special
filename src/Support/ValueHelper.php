<?php

namespace Glhd\Special\Support;

use BackedEnum;
use Glhd\Special\CreateWith;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use ReflectionClassConstant;
use Throwable;

class ValueHelper
{
	public static function getValuesFor(BackedEnum $enum): array
	{
		return (new static($enum))->all();
	}
	
	/**
	 * @param \Glhd\Special\EloquentBacking $enum
	 */
	public function __construct(
		protected BackedEnum $enum
	) {
	}
	
	public function all(): array
	{
		return array_merge(
			$this->getFactoryAttributes(),
			$this->getDefaultAttributes(),
		);
	}
	
	protected function getFactoryAttributes(): array
	{
		if (App::runningUnitTests() && $factory = $this->factory()) {
			$values = $factory->raw();
			
			foreach (['name', 'display_name', 'label', 'title', 'description'] as $attribute) {
				if (array_key_exists($attribute, $values)) {
					$values[$attribute] = Str::headline($this->enum->name);
				}
			}
			
			return $values;
		}
		
		return [];
	}
	
	protected function getDefaultAttributes(): array
	{
		$defaults = (new ReflectionClassConstant($this->enum::class, $this->enum->name))
			->getAttributes(CreateWith::class);
		
		if (! count($defaults)) {
			return [];
		}
		
		return $defaults[0]->newInstance()->attributes;
	}
	
	protected function factory(): ?Factory
	{
		try {
			$class_name = (string) $this->enum->modelClass();
			
			// TODO: We need to account for `newFactory()` on models, but it's protected...
			// TODO: But generally overriding `newFactory()` is uncommon, so it's OK to figure this out later
			// if (method_exists($class_name, 'newFactory') && $factory = $class_name::newFactory()) {
			// 	return $factory;
			// }
			
			// Then, try the Factory resolver
			if (class_exists(Factory::resolveFactoryName($class_name))) {
				return Factory::factoryForModel($class_name);
			}
		} catch (Throwable) {
			// If there's an issue finding the factory, that's OK. We'll just ignore it.
		}
		
		return null;
	}
}
