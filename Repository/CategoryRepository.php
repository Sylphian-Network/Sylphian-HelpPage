<?php

namespace Sylphian\HelpPage\Repository;

use XF\Entity\HelpPage;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class CategoryRepository extends Repository
{
	/**
	 * @param bool $activeOnly
	 * @return Finder
	 */
	public function findCategoriesForList(bool $activeOnly = false): Finder
	{
		$finder = $this->finder('Sylphian\HelpPage:Category')
			->order('display_order');

		if ($activeOnly)
		{
			$finder->where('active', 1);
		}

		return $finder;
	}

	/**
	 * @param AbstractCollection|HelpPage[] $pages
	 * @param bool $activeOnly
	 * @return array
	 */
	public function groupHelpPagesByCategory(AbstractCollection|array $pages, bool $activeOnly = false): array
	{
		$categories = $this->findCategoriesForList($activeOnly)->fetch();
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
			else if (!$categoryId || !$activeOnly)
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
