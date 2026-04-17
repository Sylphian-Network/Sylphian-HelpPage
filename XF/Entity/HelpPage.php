<?php

namespace Sylphian\HelpPage\XF\Entity;

use Sylphian\HelpPage\Entity\Category;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int $sylphian_category_id
 *
 * RELATIONS
 * @property Category|null $SylphianCategory
 */
class HelpPage extends XFCP_HelpPage
{
	public static function getStructure(Structure $structure)
	{
		$structure = parent::getStructure($structure);

		$structure->columns['sylphian_category_id'] = ['type' => Entity::UINT, 'default' => 0];

		$structure->relations['SylphianCategory'] = [
			'entity' => 'Sylphian\HelpPage:Category',
			'type' => Entity::TO_ONE,
			'conditions' => [
				['category_id', '=', '$sylphian_category_id'],
			],
			'primary' => true,
		];

		return $structure;
	}
}
