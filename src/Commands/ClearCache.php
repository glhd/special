<?php

namespace Glhd\Special\Commands;

use Glhd\Special\Support\KeyMap;
use Illuminate\Console\Command;

class ClearCache extends Command
{
	protected $signature = 'cache:clear-special-keys';
	
	protected $description = 'Clear the cache of special database keys';
	
	public function handle(): int
	{
		if ($this->getLaravel()->make(KeyMap::class)->clear()) {
			$this->info('Special cache cleared!');
			return self::SUCCESS;
		}
		
		$this->error('Unable to clear special cache.');
		return self::FAILURE;
	}
}
