<?php

namespace Glhd\Special;

use Attribute;

#[Attribute]
class CreateWith
{
	public function __construct(
		public array $attributes
	) {
	}
}
