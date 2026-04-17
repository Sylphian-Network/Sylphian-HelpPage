<?php

namespace Sylphian\HelpPage\Entity;

use XF\Entity\HelpPage;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null $category_id
 * @property string $title
 * @property int $display_order
 *
 * RELATIONS
 * @property AbstractCollection|HelpPage[] $HelpPages
 */
class Category extends Entity
{
	public function getId(): ?int
	{
		return $this->category_id;
	}

	public static function getStructure(Structure $structure): Structure
	{
		$structure->table = 'xf_sylphian_help_page_categories';
		$structure->shortName = 'Sylphian\HelpPage:Category';
		$structure->primaryKey = 'category_id';
		$structure->columns = [
			'category_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'display_order' => ['type' => self::UINT, 'default' => 1],
		];
		$structure->getters = [
			'id' => true,
		];
		$structure->relations = [
			'HelpPages' => [
				'entity' => 'XF:HelpPage',
				'type' => self::TO_MANY,
				'conditions' => [
					['sylphian_category_id', '=', '$category_id'],
				],
				'key' => 'page_id',
			],
		];

		return $structure;
	}
}
