<?php

namespace Glhd\Special;

use Attribute;

#[Attribute]
class DefaultAttributes
{
	public function __construct(
		public array $attributes
	) {
	}
}
