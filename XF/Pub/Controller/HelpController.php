<?php

namespace Sylphian\HelpPage\XF\Pub\Controller;

use Sylphian\HelpPage\Repository\CategoryRepository;
use XF\Mvc\Reply\View;

class HelpController extends XFCP_HelpController
{
	protected function handleHelpPage($pageName)
	{
		$reply = parent::handleHelpPage($pageName);

		if ($reply instanceof View)
		{
			/** @var \Sylphian\HelpPage\XF\Entity\HelpPage $page */
			$page = $reply->getParam('page');
			$category = $page->SylphianCategory;
			if ($category && !$category->active)
			{
				return $this->error(\XF::phrase('requested_page_not_found'), 404);
			}
		}

		return $reply;
	}

	protected function addWrapperParams(View $view, $selected)
	{
		$reply = parent::addWrapperParams($view, $selected);

		if ($reply instanceof View)
		{
			$pages = $reply->getParam('pages');
			if ($pages)
			{
				$categoryRepo = $this->repository(CategoryRepository::class);
				$reply->setParam('groupedPages', $categoryRepo->groupHelpPagesByCategory($pages, true));
			}
		}

		return $reply;
	}
}
