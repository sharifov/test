<?php

use yii\db\Migration;

/**
 * Class m190822_143638_create_permissions_for_lead_and_case_sections
 */
class m190822_143638_create_permissions_for_lead_and_case_sections extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadSection = $auth->createPermission('leadSection');
        $leadSection->description = 'Access to Lead Section';
        $auth->add($leadSection);

        foreach (['admin', 'agent', 'supervision', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            $auth->addChild($role, $leadSection);
        }

        $caseSection = $auth->createPermission('caseSection');
        $caseSection->description = 'Access to Case Section';
        $auth->add($caseSection);

        foreach (['admin', 'sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            $auth->addChild($role, $caseSection);
        }

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

        $leadSection = $auth->getPermission('leadSection');
        $auth->remove($leadSection);

        $caseSection = $auth->getPermission('caseSection');
        $auth->remove($caseSection);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
