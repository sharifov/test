<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200116_072227_add_role_exchange_senior
 */
class m200116_072227_add_role_exchange_senior extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $salesSenior = $auth->createRole(Employee::ROLE_EXCHANGE_SENIOR);
        $salesSenior->description = 'Exchange Senior';
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

        if ($salesSenior = $auth->getRole(Employee::ROLE_EXCHANGE_SENIOR)) {
            $auth->remove($salesSenior);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
