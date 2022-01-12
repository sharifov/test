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
        (new RbacMoveToSrc())->move(env('app.path') . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'rbac');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
