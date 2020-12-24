<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201218_114013_add_permission_client_country
 */
class m201218_114013_add_permission_client_country extends Migration
{
    private $permissionName = 'global/client/marketing_country/edit';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $editLocale = $auth->createPermission($this->permissionName);
        $editLocale->description = 'Edit Client Marketing country';
        $auth->add($editLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission($this->permissionName)) {
            $auth->remove($permission);
        }
    }
}
