<?php

use sales\rbac\rules\lead\view\LeadViewBookFailedRule;
use yii\db\Migration;

/**
 * Class m200424_163653_add_permission_lead_view_failed_bookings
 */
class m200424_163653_add_permission_lead_view_failed_bookings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadView = $auth->getPermission('lead/view');

        $leadViewBookFailedRule = new LeadViewBookFailedRule();
        $auth->add($leadViewBookFailedRule);
        $leadViewBookFailed = $auth->createPermission('lead/view_BookFailed');
        $leadViewBookFailed->description = 'Lead View status Book Failed';
        $leadViewBookFailed->ruleName = $leadViewBookFailedRule->name;
        $auth->add($leadViewBookFailed);
        $auth->addChild($leadViewBookFailed, $leadView);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewBookFailed = $auth->getPermission('lead/view_BookFailed')) {
            $auth->remove($leadViewBookFailed);
        }

        if ($leadViewBookFailedRule = $auth->getRule('LeadViewBookFailedRule')) {
            $auth->remove($leadViewBookFailedRule);
        }
    }
}
