<?php

namespace Sylphian\HelpPage\Repository;

use XF\Entity\HelpPage;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Category extends Repository
{
	/**
	 * @return Finder
	 */
	public function findCategoriesForList(): Finder
	{
		return $this->finder('Sylphian\HelpPage:Category')
			->order('display_order');
	}

	/**
	 * @param AbstractCollection|HelpPage[] $pages
	 * @return array
	 */
	public function groupHelpPagesByCategory(AbstractCollection|array $pages): array
	{
		$categories = $this->findCategoriesForList()->fetch();
		$grouped = [];

		foreach ($categories AS $category)
		{
			$grouped[$category->category_id] = [
				'category' => $category,
				'pages' => [],
			];
		}

		$uncategorised = [];

		foreach ($pages AS $page)
		{
			/** @var \Sylphian\HelpPage\XF\Entity\HelpPage $page */
			$categoryId = $page->sylphian_category_id;
			if ($categoryId && isset($grouped[$categoryId]))
			{
				$grouped[$categoryId]['pages'][] = $page;
			}
			else
			{
				$uncategorised[] = $page;
			}
		}

		if ($uncategorised)
		{
			$grouped[0] = [
				'category' => null,
				'pages' => $uncategorised,
			];
		}

		return $grouped;
	}
}
