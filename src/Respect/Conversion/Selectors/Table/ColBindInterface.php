<?php

namespace Respect\Conversion\Selectors\Table;

interface ColBindInterface
{
	public function bindToTableCol(Col $selector);
}