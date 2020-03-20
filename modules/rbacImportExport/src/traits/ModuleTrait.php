<?php
namespace modules\rbacImportExport\src\traits;


use yii\rbac\ManagerInterface;

/**
 * Trait ModuleTrait
 * @package modules\rbacImportExport\src\traits
 *
 * @property ManagerInterface $authManager
 */
trait ModuleTrait
{
	public function getModule(): ?\yii\base\Module
	{
		return \Yii::$app->getModule('rbac-import-export');
	}

	public function getAuthManager(): ManagerInterface
	{
		return $this->getModule()->authManager;
	}
}