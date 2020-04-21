<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200116_082933_add_role_support_senior
 */
class m200116_082933_add_role_support_senior extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $salesSenior = $auth->createRole(Employee::ROLE_SUPPORT_SENIOR);
        $salesSenior->description = 'Support Senior';
        $auth->add($salesSenior);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($salesSenior = $auth->getRole(Employee::ROLE_SUPPORT_SENIOR)) {
            $auth->remove($salesSenior);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
