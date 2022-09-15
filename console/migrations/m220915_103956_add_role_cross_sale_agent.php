<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m220915_103956_add_role_cross_sale_agent
 */
class m220915_103956_add_role_cross_sale_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $role = $auth->createRole(Employee::ROLE_CROSS_SALE_AGENT);
        $role->description = 'Cross Sale agent';
        $auth->add($role);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($role = $auth->getRole(Employee::ROLE_CROSS_SALE_AGENT)) {
            $auth->remove($role);
        }
    }
}
