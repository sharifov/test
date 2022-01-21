<?php

use src\rbac\rules\call\PhoneWidgetTransferToUserIncomingRule;
use src\rbac\rules\call\PhoneWidgetTransferToUserOutgoingRule;
use yii\db\Migration;

/**
 * Class m210506_202207_add_permission_transfer_to_user_in_out_call
 */
class m210506_202207_add_permission_transfer_to_user_in_out_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $transferToUserPermission = $auth->getPermission('PhoneWidget_TransferToUser');
        if (!$transferToUserPermission) {
            return;
        }

        $transferToUserIncomingRule = new PhoneWidgetTransferToUserIncomingRule();
        $auth->add($transferToUserIncomingRule);
        $transferToUserIncomingPermission = $auth->createPermission('PhoneWidget_TransferToUser_Incoming');
        $transferToUserIncomingPermission->description = 'Transfer to user Incoming call';
        $transferToUserIncomingPermission->ruleName = $transferToUserIncomingRule->name;
        $auth->add($transferToUserIncomingPermission);
        $auth->addChild($transferToUserIncomingPermission, $transferToUserPermission);

        $transferToUserOutgoingRule = new PhoneWidgetTransferToUserOutgoingRule();
        $auth->add($transferToUserOutgoingRule);
        $transferToUserOutgoingPermission = $auth->createPermission('PhoneWidget_TransferToUser_Outgoing');
        $transferToUserOutgoingPermission->description = 'Transfer to user Outgoing call';
        $transferToUserOutgoingPermission->ruleName = $transferToUserOutgoingRule->name;
        $auth->add($transferToUserOutgoingPermission);
        $auth->addChild($transferToUserOutgoingPermission, $transferToUserPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'PhoneWidgetTransferToUserIncomingRule',
            'PhoneWidgetTransferToUserOutgoingRule',
        ];

        $permissions = [
            'PhoneWidget_TransferToUser_Incoming',
            'PhoneWidget_TransferToUser_Outgoing',
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

        foreach ($rules as $ruleName) {
            if ($rule = $auth->getRule($ruleName)) {
                $auth->remove($rule);
            }
        }
    }
}
