<?php

use yii\db\Migration;

/**
 * Class m210104_120207_add_phone_widget_call_disabled_permission
 */
class m210104_120207_add_phone_widget_call_disabled_permission extends Migration
{
    private $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->createPermission('PhoneWidget_CallRecordingDisabled');
        $permission->description = 'PhoneWidget Call Recording Disabled';
        $auth->add($permission);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $permission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('PhoneWidget_CallRecordingDisabled')) {
            $auth->remove($permission);
        }
    }
}
