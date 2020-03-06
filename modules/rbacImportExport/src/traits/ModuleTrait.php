<?php
namespace modules\rbacImportExport\src\traits;


trait ModuleTrait
{
	public function getModule(): ?\yii\base\Module
	{
		return \Yii::$app->getModule('rbac-import-export');
	}
}