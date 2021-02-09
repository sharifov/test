<?php

use yii\db\Migration;

/**
 * Class m200701_145233_add_permission_coupon_request_full_list
 */
class m200701_145233_add_permission_coupon_request_full_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $requestAllList = $auth->createPermission('coupon/request-full-list');
        $auth->add($requestAllList);

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

        if ($requestAllList = $auth->getPermission('coupon/request-full-list')) {
            $auth->remove($requestAllList);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
