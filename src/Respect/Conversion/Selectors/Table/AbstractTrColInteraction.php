<?php

namespace Respect\Conversion\Selectors\Table;

use Respect\Conversion\Selectors\Common\AbstractSelector;

abstract class AbstractTrColInteraction extends AbstractSelector
{
	public function bindToCol(Col $target)
	{
		$mirror = new \ReflectionClass(get_called_class());
		return $mirror->newInstanceArgs(array_merge($this->cols, $target->cols));
	}

	public function bindToTr(Tr $target)
	{
		$mirror = new \ReflectionClass(get_called_class());
		return $mirror->newInstanceArgs(array_merge($this->lines, $target->lines));
	}

	public function bindTrCol(Tr $tr, Col $col)
	{
		$tds = array();

		if ($tr->lines && $col->cols)
			foreach ($tr->lines as $trNo)
				foreach ($col->cols as $colNo)
					$tds[] = array($trNo, $colNo);
		elseif ($tr->lines)
			foreach ($tr->lines as $tr)
				$tds[] = array($tr, null);
		elseif ($col->cols)
			foreach ($col->cols as $col)
				$tds[] = array(null, $col);

		$mirror = new \ReflectionClass(__NAMESPACE__.'\\Td');
		return $mirror->newInstanceArgs($tds);	
	}

}