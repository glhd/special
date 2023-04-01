<?php

namespace Glhd\Guidepost;

use Attribute;

#[Attribute]
class DefaultAttributes
{
	public function __construct(
		public array $attributes
	) {
	}
}
