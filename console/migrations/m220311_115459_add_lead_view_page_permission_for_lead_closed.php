<?php

use src\rbac\rules\lead\view\LeadViewClosedRule;
use yii\db\Migration;

/**
 * Class m220311_115459_add_lead_view_page_permission_for_lead_closed
 */
class m220311_115459_add_lead_view_page_permission_for_lead_closed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadView = $auth->getPermission('lead/view');

        $leadViewClosedRule = new LeadViewClosedRule();
        $leadViewClosedRule->name = 'LeadViewClosedRule';
        $auth->add($leadViewClosedRule);
        $leadViewClosed = $auth->createPermission('lead/view_Closed');
        $leadViewClosed->description = 'Lead View status Closed';
        $leadViewClosed->ruleName = $leadViewClosedRule->name;
        $auth->add($leadViewClosed);
        $auth->addChild($leadViewClosed, $leadView);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $arr = [
            ['permission' => 'lead/view_Closed', 'rule' => 'LeadViewClosedRule'],
        ];

        foreach ($arr as $item) {
            if ($permission = $auth->getPermission($item['permission'])) {
                $auth->remove($permission);
            }
            if ($rule = $auth->getRule($item['rule'])) {
                $auth->remove($rule);
            }
        }
    }
}
