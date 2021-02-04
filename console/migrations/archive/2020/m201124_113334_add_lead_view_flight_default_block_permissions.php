<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\flightDefaultBlock\LeadViewFlightDefaultBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\flightDefaultBlock\LeadViewFlightDefaultBlockGroupRule;
use sales\rbac\rules\lead\view\flightDefaultBlock\LeadViewFlightDefaultBlockIsOwnerRule;

/**
 * Class m201124_113334_add_lead_view_flight_default_block_permissions
 */
class m201124_113334_add_lead_view_flight_default_block_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadViewFlightDefaultViewPermission = $auth->createPermission('lead-view/flight-default/view');
        $leadViewFlightDefaultViewPermission->description = 'Flight Default view';
        $auth->add($leadViewFlightDefaultViewPermission);

        $leadViewFlightDefaultViewAllPermission = $auth->createPermission('lead-view/flight-default/view/all');
        $leadViewFlightDefaultViewAllPermission->description = 'Flight Default view all';
        $auth->add($leadViewFlightDefaultViewAllPermission);
        $auth->addChild($leadViewFlightDefaultViewAllPermission, $leadViewFlightDefaultViewPermission);

        $leadViewFlightDefaultBlockOwnerRule = new LeadViewFlightDefaultBlockIsOwnerRule();
        $auth->add($leadViewFlightDefaultBlockOwnerRule);
        $leadViewFlightDefaultViewOwnerPermission = $auth->createPermission('lead-view/flight-default/view/owner');
        $leadViewFlightDefaultViewOwnerPermission->description = 'Lead View Flight Default Block user is Owner';
        $leadViewFlightDefaultViewOwnerPermission->ruleName = $leadViewFlightDefaultBlockOwnerRule->name;
        $auth->add($leadViewFlightDefaultViewOwnerPermission);
        $auth->addChild($leadViewFlightDefaultViewOwnerPermission, $leadViewFlightDefaultViewPermission);

        $leadViewFlightDefaultBlockEmptyRule = new LeadViewFlightDefaultBlockEmptyOwnerRule();
        $auth->add($leadViewFlightDefaultBlockEmptyRule);
        $leadViewFlightDefaultViewEmptyPermission = $auth->createPermission('lead-view/flight-default/view/empty');
        $leadViewFlightDefaultViewEmptyPermission->description = 'Lead View Flight Default Block Owner is empty';
        $leadViewFlightDefaultViewEmptyPermission->ruleName = $leadViewFlightDefaultBlockEmptyRule->name;
        $auth->add($leadViewFlightDefaultViewEmptyPermission);
        $auth->addChild($leadViewFlightDefaultViewEmptyPermission, $leadViewFlightDefaultViewPermission);

        $leadViewFlightDefaultBlockGroupRule = new LeadViewFlightDefaultBlockGroupRule();
        $auth->add($leadViewFlightDefaultBlockGroupRule);
        $leadViewFlightDefaultViewGroupPermission = $auth->createPermission('lead-view/flight-default/view/group');
        $leadViewFlightDefaultViewGroupPermission->description = 'Lead View Flight Default Block user is in common group with owner';
        $leadViewFlightDefaultViewGroupPermission->ruleName = $leadViewFlightDefaultBlockGroupRule->name;
        $auth->add($leadViewFlightDefaultViewGroupPermission);
        $auth->addChild($leadViewFlightDefaultViewGroupPermission, $leadViewFlightDefaultViewPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewFlightDefaultBlockIsOwnerRule',
            'LeadViewFlightDefaultBlockEmptyOwnerRule',
            'LeadViewFlightDefaultBlockGroupRule'
        ];

        $permissions = [
            'lead-view/flight-default/view',
            'lead-view/flight-default/view/all',
            'lead-view/flight-default/view/owner',
            'lead-view/flight-default/view/empty',
            'lead-view/flight-default/view/group'
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
