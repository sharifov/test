<?php

use common\models\Employee;
use sales\auth\Auth;
use sales\helpers\UserCallIdentity;
use yii\db\Migration;

/**
 * Class m211130_142552_remove_refresh_token_cache
 */
class m211130_142552_remove_refresh_token_cache extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $users = Employee::find()->select(['id'])->asArray()->all();
        foreach ($users as $user) {
            $username = UserCallIdentity::getId($user['id']);
            $cacheKey = 'jwt_token_' . $username;
            \Yii::$app->cache->delete($cacheKey);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
