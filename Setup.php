<?php

namespace Sylphian\HelpPage;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function installStep1(): void
	{
		$this->schemaManager()->createTable('xf_sylphian_help_page_categories', function (Create $table)
		{
			$table->addColumn('category_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('display_order', 'int')->setDefault(1);
			$table->addColumn('active', 'tinyint', 1)->setDefault(1);

			$table->addPrimaryKey('category_id');
		});
	}

	public function installStep2(): void
	{
		$this->schemaManager()->alterTable('xf_help_page', function (Alter $table)
		{
			$table->addColumn('sylphian_category_id', 'int')->setDefault(0);
			$table->addKey('sylphian_category_id');
		});
	}

	public function upgrade1000052Step1(): void
	{
		$this->schemaManager()->alterTable('xf_sylphian_help_page_categories', function (Alter $table)
		{
			$table->addColumn('active', 'tinyint', 1)->setDefault(1);
		});
	}

	public function upgrade1000052Step2(): void
	{
		$this->schemaManager()->alterTable('xf_sylphian_help_page_categories', function (Alter $table)
		{
			$table->dropColumns('description');
		});
	}

	public function uninstallStep1(): void
	{
		$this->schemaManager()->dropTable('xf_sylphian_help_page_categories');
	}

	public function uninstallStep2(): void
	{
		$this->schemaManager()->alterTable('xf_help_page', function (Alter $table)
		{
			$table->dropColumns('sylphian_category_id');
		});
	}
}
