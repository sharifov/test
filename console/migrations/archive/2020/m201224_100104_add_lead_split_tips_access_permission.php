<?php

use yii\db\Migration;
use console\migrations\RbacMigrationService;
use sales\rbac\rules\lead\splitRules\SplitTipsLeadSoldRule;

/**
 * Class m201224_100104_add_lead_split_tips_access_permission
 */
class m201224_100104_add_lead_split_tips_access_permission extends Migration
{
    private string $leadSplitTips = 'lead/split-tips';
    private array $systemRoles = [];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $splitTipsLeadSoldRule = new SplitTipsLeadSoldRule();
        $auth->add($splitTipsLeadSoldRule);

        $leadSplitTipsPermission = $auth->createPermission($this->leadSplitTips);
        $leadSplitTipsPermission->description = 'Lead split tips';
        $leadSplitTipsPermission->ruleName = $splitTipsLeadSoldRule->name;
        $auth->add($leadSplitTipsPermission);

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/lead/split-tips') {
                    $this->systemRoles[] = $role->name;
                }
            }
        }

        (new RbacMigrationService())->up([$this->leadSplitTips], $this->systemRoles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/lead/split-tips') {
                    $this->systemRoles[] = $role->name;
                }
            }
        }

        (new RbacMigrationService())->down([$this->leadSplitTips], $this->systemRoles);

        if ($permission = $auth->getPermission($this->leadSplitTips)) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('SplitTipsLeadSoldRule')) {
            $auth->remove($rule);
        }
    }
}
