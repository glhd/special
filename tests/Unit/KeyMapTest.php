<?php

namespace Glhd\Special\Tests\Unit;

use Glhd\Special\Support\KeyMap;
use Glhd\Special\Tests\TestCase;
use Illuminate\Cache\Repository;
use Illuminate\Support\Str;
use Mockery;

class KeyMapTest extends TestCase
{
	public function test_it_respects_0_ttl(): void
	{
		$mock = Mockery::mock(Repository::class);
		
		$mock->shouldNotReceive('get', 'put', 'forget', 'has');
		
		(new KeyMap($mock, 0))->get($this->makeEnum()[0]);
	}
	
	public function test_it_can_be_cleared(): void
	{
		$mock = Mockery::mock(Repository::class);
		
		$mock->shouldReceive('forget')
			->with('glhd-special:keymap')
			->once()
			->andReturn(true);
		
		$this->assertTrue((new KeyMap($mock))->clear());
	}
	
	public function test_it_returns_false_if_not_cleared(): void
	{
		$mock = Mockery::mock(Repository::class);
		
		$mock->shouldReceive('forget')
			->with('glhd-special:keymap')
			->once()
			->andReturn(false);
		
		$this->assertFalse((new KeyMap($mock))->clear());
	}
	
	public function test_it_prunes_on_save(): void
	{
		[$a, $fqcn_a] = $this->makeEnum('A', 'Foo', 1);
		[$b, $fqcn_b] = $this->makeEnum('B', 'Foo', 2);
		
		$mock = Mockery::mock(Repository::class);
		
		$mock->shouldReceive('get')->once();
		
		$mock->shouldReceive('put')
			->once()
			->with('glhd-special:keymap', [ltrim("{$fqcn_a}:1", '\\') => 1], 17);
		
		$mock->shouldReceive('put')
			->once()
			->with('glhd-special:keymap', [ltrim("{$fqcn_b}:2", '\\') => 2], 17);
		
		$mock = new KeyMap($mock, ttl: 17, limit: 1);
		
		$mock->get($a);
		$mock->get($b);
	}
	
	public function test_it_should_not_prune_if_limit_is_0(): void
	{
		$mock = Mockery::mock(Repository::class);
		
		$mock->shouldReceive('get')->atLeast()->once();
		$mock->shouldReceive('put')->atLeast()->once();
		
		$mock = new KeyMap($mock, ttl: 17, limit: 0);
		
		for ($i = 0; $i < 1000; $i++) {
			$mock->get($this->makeEnum("TestEnum_{$i}", "Case_{$i}", $i)[0]);
		}
		
		$this->assertCount(1000, $mock->toArray());
	}
	
	protected function makeEnum(?string $class = null, ?string $case = null, int|string|null $value = null): array
	{
		$namespace = 'TestEnums_'.preg_replace('/\D/', '', microtime(true));
		$class ??= 'TestEnum';
		$case ??= Str::random();
		$value ??= random_int(1, PHP_INT_MAX);
		$type = get_debug_type($value);
		
		$value = var_export($value, true);
		
		$fqcn = "\\{$namespace}\\{$class}";
		
		$src = <<<PHP
		namespace {$namespace} {
			enum {$class}: {$type} {
				case {$case} = {$value};
				
				public function singleton()
				{
					return new class {
						public function getKey()
						{
							return {$value};
						}
					};
				}
			}
		}
		PHP;

		eval($src);
		
		return [$fqcn::cases()[0], $fqcn, $case, $value];
	}
}
