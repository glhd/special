<?php

namespace Glhd\Special\Exceptions;

use BackedEnum;
use RuntimeException;

class BackingModelNotFound extends RuntimeException
{
	public function __construct(
		public BackedEnum $special_enum
	) {
		$basename = class_basename($this->special_enum);
		
		parent::__construct("Unable to find backing model for '{$basename}::{$this->special_enum->name}'");
	}
}
