<?php

use yii\db\Migration;

/**
 * Class m190807_125432_remove_permision_export_leads_for_supervision
 */
class m190807_125432_remove_permision_export_leads_for_supervision extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $routeDuplicate = $auth->getPermission('/leads/export');
        $supervision = $auth->getRole('supervision');
        if ($auth->hasChild($supervision, $routeDuplicate)) {
            $auth->removeChild($supervision, $routeDuplicate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $routeDuplicate = $auth->getPermission('/leads/export');
        $supervision = $auth->getRole('supervision');
        try {
            $auth->addChild($supervision, $routeDuplicate);
        } catch (\Throwable $e) {
            echo "Can't add child /leads/export for supervision";
        }
    }

}
