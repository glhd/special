<?php

namespace Glhd\Special\Exceptions;

use BackedEnum;
use Illuminate\Database\RecordsNotFoundException;

class BackingModelNotFound extends RecordsNotFoundException
{
	public function __construct(
		public BackedEnum $special_enum
	) {
		$basename = class_basename($this->special_enum);
		
		parent::__construct("Unable to find model for '{$basename}::{$this->special_enum->name}'");
	}
}
