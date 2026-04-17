<?php

namespace Sylphian\HelpPage\XF\Pub\Controller;

use Sylphian\HelpPage\Repository\Category;
use XF\Mvc\Reply\View;

class HelpController extends XFCP_HelpController
{
	protected function addWrapperParams(View $view, $selected)
	{
		$reply = parent::addWrapperParams($view, $selected);

		if ($reply instanceof View)
		{
			$pages = $reply->getParam('pages');
			if ($pages)
			{
				$categoryRepo = $this->repository(Category::class);
				$reply->setParam('groupedPages', $categoryRepo->groupHelpPagesByCategory($pages));
			}
		}

		return $reply;
	}
}
