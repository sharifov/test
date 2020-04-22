<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200113_155528_add_role_sales_senior
 */
class m200113_155528_add_role_sales_senior extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $salesSenior = $auth->createRole(Employee::ROLE_SALES_SENIOR);
        $salesSenior->description = 'Sales Senior';
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

        if ($salesSenior = $auth->getRole(Employee::ROLE_SALES_SENIOR)) {
            $auth->remove($salesSenior);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
