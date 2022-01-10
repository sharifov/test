<?php

use sales\rbac\rules\lead\view\LeadViewAlternativeRule;
use yii\db\Migration;

/**
 * Class m210127_050320_add_permission_lead_alternative_view
 */
class m210127_050320_add_permission_lead_alternative_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadView = $auth->getPermission('lead/view');

        $leadViewAlternativeRule = new LeadViewAlternativeRule();
        $auth->add($leadViewAlternativeRule);
        $leadViewAlternative = $auth->createPermission('lead/view_Alternative');
        $leadViewAlternative->description = 'Lead View Alternative';
        $leadViewAlternative->ruleName = $leadViewAlternativeRule->name;
        $auth->add($leadViewAlternative);
        $auth->addChild($leadViewAlternative, $leadView);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewAlternative = $auth->getPermission('lead/view_Alternative')) {
            $auth->remove($leadViewAlternative);
        }

        if ($leadViewAlternativeRule = $auth->getRule('LeadViewAlternativeRule')) {
            $auth->remove($leadViewAlternativeRule);
        }
    }
}
