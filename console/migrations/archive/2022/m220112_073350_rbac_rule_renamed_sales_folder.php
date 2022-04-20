<?php

use src\rbac\RbacMoveToSrc;
use yii\db\Migration;

/**
 * Class m220112_073350_rbac_rule_renamed_sales_folder
 */
class m220112_073350_rbac_rule_renamed_sales_folder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $subDir = 'src';
        (new RbacMoveToSrc())->move(env('APP_PATH') . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . 'rbac', $subDir);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $subDir = 'sales';
        (new RbacMoveToSrc())->move(env('APP_PATH') . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . 'rbac', $subDir);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
