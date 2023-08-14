<?php

namespace Glhd\Special\Support;

use BackedEnum;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Support\Arrayable;

class KeyMap implements Arrayable
{
	protected const CACHE_KEY = 'glhd-special:keymap';
	
	protected ?array $map = null;
	
	public function __construct(
		protected Repository $cache,
		protected int|DateTimeInterface|DateInterval $ttl = 3600,
		protected int $limit = 50,
	) {
	}
	
	/**
	 * Get the primary key of an Eloquent-backed enum.
	 *
	 * @param \Glhd\Special\EloquentBacking $enum
	 * @return string|int
	 */
	public function get(BackedEnum $enum): string|int
	{
		if (0 === $this->ttl) {
			return $enum->singleton()->getKey();
		}
		
		return $this->map()[$this->getKey($enum)] ?? $this->getAndCache($enum);
	}
	
	/**
	 * Clear the key map and cache.
	 *
	 * @return bool
	 */
	public function clear(): bool
	{
		if ($this->cache->forget(static::CACHE_KEY)) {
			$this->map = null;
			return true;
		}
		
		return false;
	}
	
	public function toArray(): array
	{
		return $this->map();
	}
	
	/**
	 * @param \Glhd\Special\EloquentBacking $enum
	 * @return string|int
	 */
	protected function getAndCache(BackedEnum $enum): string|int
	{
		try {
			return $this->map[$this->getKey($enum)] = $enum->singleton()->getKey();
		} finally {
			$this->write();
		}
	}
	
	protected function write(): void
	{
		$this->prune();
		
		$this->cache->put(static::CACHE_KEY, $this->map, $this->ttl);
	}
	
	protected function prune(): void
	{
		if (0 === $this->limit) {
			return;
		}
		
		// Only keep `$this->limit` number of items in the cached map. Using a reverse
		// offset with an associative array effectively keeps the "last N" items.
		$this->map = array_slice($this->map, -1 * $this->limit, $this->limit, true);
	}
	
	protected function map(): array
	{
		return $this->map ??= $this->cache->get(self::CACHE_KEY) ?? [];
	}
	
	protected function getKey(BackedEnum $value): string
	{
		return $value::class.':'.$value->value;
	}
}
