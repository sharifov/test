<?php

use yii\db\Migration;

/**
 * Class m200318_162014_add_permission_for_rbac_import_export
 */
class m200318_162014_add_permission_for_rbac_import_export extends Migration
{
	public $route = ['/rbac-import-export/*'];

	public $roles = [
		\common\models\Employee::ROLE_SUPER_ADMIN
	];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		(new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		(new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}
}
