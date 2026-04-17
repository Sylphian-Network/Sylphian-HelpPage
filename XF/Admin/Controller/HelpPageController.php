<?php

namespace Sylphian\HelpPage\XF\Admin\Controller;

use Sylphian\HelpPage\Repository\Category;
use XF\ControllerPlugin\DeletePlugin;
use XF\Entity\HelpPage;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View;

class HelpPageController extends XFCP_HelpPageController
{
	public function actionIndex()
	{
		$reply = parent::actionIndex();

		if ($reply instanceof View)
		{
			$pages = $reply->getParam('pages') ?: [];
			$categoryRepo = $this->repository(Category::class);

			$reply->setParam('groupedPages', $categoryRepo->groupHelpPagesByCategory($pages));
		}

		return $reply;
	}

	protected function pageAddEdit(HelpPage $page)
	{
		$reply = parent::pageAddEdit($page);

		if ($reply instanceof View)
		{
			$categoryRepo = $this->repository(Category::class);
			$reply->setParam('categories', $categoryRepo->findCategoriesForList()->fetch()->pluckNamed('title', 'category_id'));
		}

		return $reply;
	}

	protected function pageSaveProcess(HelpPage $page)
	{
		$form = parent::pageSaveProcess($page);

		$form->setupEntityInput($page, $this->filter([
			'sylphian_category_id' => 'uint',
		]));

		return $form;
	}

	protected function categoryAddEdit(\Sylphian\HelpPage\Entity\Category $category)
	{
		$viewParams = [
			'category' => $category,
		];
		return $this->view('Sylphian\HelpPage:Category\Edit', 'sylphian_help_category_edit', $viewParams);
	}

	public function actionCategoryAdd()
	{
		$category = $this->em()->create(\Sylphian\HelpPage\Entity\Category::class);
		return $this->categoryAddEdit($category);
	}

	public function actionCategoryEdit(ParameterBag $params)
	{
		$categoryId = $params['category_id'] ?? $this->filter('category_id', 'uint');
		$category = $this->assertCategoryExists($categoryId);
		return $this->categoryAddEdit($category);
	}

	protected function categorySaveProcess(\Sylphian\HelpPage\Entity\Category $category)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'title' => 'str',
			'display_order' => 'uint',
		]);

		$form->basicEntitySave($category, $input);

		return $form;
	}

	public function actionCategorySave(ParameterBag $params)
	{
		$this->assertPostOnly();

		$categoryId = $params['category_id'] ?? $this->filter('category_id', 'uint');
		if ($categoryId)
		{
			$category = $this->assertCategoryExists($params['category_id']);
		}
		else
		{
			$category = $this->em()->create(\Sylphian\HelpPage\Entity\Category::class);
		}

		$form = $this->categorySaveProcess($category);
		$form->run();

		return $this->redirect($this->buildLink('help-pages'));
	}

	public function actionCategoryDelete(ParameterBag $params)
	{
		$categoryId = $params['category_id'] ?? $this->filter('category_id', 'uint');
		$category = $this->assertCategoryExists($categoryId);

		/** @var DeletePlugin $plugin */
		$plugin = $this->plugin(DeletePlugin::class);
		return $plugin->actionDelete(
			$category,
			$this->buildLink('help-pages/category-delete', null, ['category_id' => $category->category_id]),
			$this->buildLink('help-pages/category-edit', null, ['category_id' => $category->category_id]),
			$this->buildLink('help-pages'),
			$category->getValue('title')
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param string|null $phraseKey
	 *
	 * @return \Sylphian\HelpPage\Entity\Category
	 */
	protected function assertCategoryExists(string $id, array|string|null $with = null, ?string $phraseKey = null)
	{
		return $this->assertRecordExists(\Sylphian\HelpPage\Entity\Category::class, $id, $with, $phraseKey);
	}
}
