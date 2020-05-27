<?php

use sales\rbac\rules\lead\view\LeadViewBookedRule;
use sales\rbac\rules\lead\view\LeadViewFollowUpRule;
use sales\rbac\rules\lead\view\LeadViewPendingRule;
use sales\rbac\rules\lead\view\LeadViewProcessingRule;
use sales\rbac\rules\lead\view\LeadViewRejectRule;
use sales\rbac\rules\lead\view\LeadViewSnoozeRule;
use sales\rbac\rules\lead\view\LeadViewSoldRule;
use sales\rbac\rules\lead\view\LeadViewTrashRule;
use yii\db\Migration;

/**
 * Class m200306_102043_add_lead_view_permissions
 */
class m200306_102043_add_lead_view_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadView = $auth->createPermission('lead/view');
        $leadView->description = 'Lead View';
        $auth->add($leadView);

        $leadViewPendingRule = new LeadViewPendingRule();
        $auth->add($leadViewPendingRule);
        $leadViewPending = $auth->createPermission('lead/view_Pending');
        $leadViewPending->description = 'Lead View status Pending';
        $leadViewPending->ruleName = $leadViewPendingRule->name;
        $auth->add($leadViewPending);
        $auth->addChild($leadViewPending, $leadView);

        $leadViewProcessingRule = new LeadViewProcessingRule();
        $auth->add($leadViewProcessingRule);
        $leadViewProcessing = $auth->createPermission('lead/view_Processing');
        $leadViewProcessing->description = 'Lead View status Processing';
        $leadViewProcessing->ruleName = $leadViewProcessingRule->name;
        $auth->add($leadViewProcessing);
        $auth->addChild($leadViewProcessing, $leadView);

        $leadViewRejectRule = new LeadViewRejectRule();
        $auth->add($leadViewRejectRule);
        $leadViewReject = $auth->createPermission('lead/view_Reject');
        $leadViewReject->description = 'Lead View status Reject';
        $leadViewReject->ruleName = $leadViewRejectRule->name;
        $auth->add($leadViewReject);
        $auth->addChild($leadViewReject, $leadView);

        $leadViewFollowUpRule = new LeadViewFollowUpRule();
        $auth->add($leadViewFollowUpRule);
        $leadViewFollowUp = $auth->createPermission('lead/view_FollowUp');
        $leadViewFollowUp->description = 'Lead View status FollowUp';
        $leadViewFollowUp->ruleName = $leadViewFollowUpRule->name;
        $auth->add($leadViewFollowUp);
        $auth->addChild($leadViewFollowUp, $leadView);

        $leadViewSoldRule = new LeadViewSoldRule();
        $auth->add($leadViewSoldRule);
        $leadViewSold = $auth->createPermission('lead/view_Sold');
        $leadViewSold->description = 'Lead View status Sold';
        $leadViewSold->ruleName = $leadViewSoldRule->name;
        $auth->add($leadViewSold);
        $auth->addChild($leadViewSold, $leadView);

        $leadViewTrashRule = new LeadViewTrashRule();
        $auth->add($leadViewTrashRule);
        $leadViewTrash = $auth->createPermission('lead/view_Trash');
        $leadViewTrash->description = 'Lead View status Trash';
        $leadViewTrash->ruleName = $leadViewTrashRule->name;
        $auth->add($leadViewTrash);
        $auth->addChild($leadViewTrash, $leadView);

        $leadViewBookedRule = new LeadViewBookedRule();
        $auth->add($leadViewBookedRule);
        $leadViewBooked = $auth->createPermission('lead/view_Booked');
        $leadViewBooked->description = 'Lead View status Booked';
        $leadViewBooked->ruleName = $leadViewBookedRule->name;
        $auth->add($leadViewBooked);
        $auth->addChild($leadViewBooked, $leadView);

        $leadViewSnoozeRule = new LeadViewSnoozeRule();
        $auth->add($leadViewSnoozeRule);
        $leadViewSnooze = $auth->createPermission('lead/view_Snooze');
        $leadViewSnooze->description = 'Lead View status Snooze';
        $leadViewSnooze->ruleName = $leadViewSnoozeRule->name;
        $auth->add($leadViewSnooze);
        $auth->addChild($leadViewSnooze, $leadView);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $arr = [
            ['permission' => 'lead/view_Snooze', 'rule' => 'LeadViewSnoozeRule'],
            ['permission' => 'lead/view_Booked', 'rule' => 'LeadViewBookedRule'],
            ['permission' => 'lead/view_Trash', 'rule' => 'LeadViewTrashRule'],
            ['permission' => 'lead/view_Sold', 'rule' => 'LeadViewSoldRule'],
            ['permission' => 'lead/view_FollowUp', 'rule' => 'LeadViewFollowUpRule'],
            ['permission' => 'lead/view_Reject', 'rule' => 'LeadViewRejectRule'],
            ['permission' => 'lead/view_Processing', 'rule' => 'LeadViewProcessingRule'],
            ['permission' => 'lead/view_Pending', 'rule' => 'LeadViewPendingRule'],
        ];

        foreach ($arr as $item) {
            if ($permission = $auth->getPermission($item['permission'])) {
                $auth->remove($permission);
            }
            if ($rule = $auth->getRule($item['rule'])) {
                $auth->remove($rule);
            }
        }

        if ($permission = $auth->getPermission('lead/view')) {
            $auth->remove($permission);
        }
    }
}
