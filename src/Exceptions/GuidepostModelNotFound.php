<?php

namespace Glhd\Guidepost\Exceptions;

use BackedEnum;
use Illuminate\Database\RecordsNotFoundException;

class GuidepostModelNotFound extends RecordsNotFoundException
{
	public function __construct(
		public BackedEnum $guidepost
	) {
		$basename = class_basename($this->guidepost);
		
		parent::__construct("Unable to find model for '{$basename}::{$this->guidepost->name}'");
	}
}
