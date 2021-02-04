<?php

use yii\db\Migration;
use sales\rbac\rules\lead\splitRules\SplitProfitLeadSoldRule;
use console\migrations\RbacMigrationService;

/**
 * Class m201223_152900_add_lead_split_profit_access_permission
 */
class m201223_152900_add_lead_split_profit_access_permission extends Migration
{
    private string $leadSplitProfit = 'lead/split-profit';
    private array $systemRoles = [];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $splitProfitLeadSoldRule = new SplitProfitLeadSoldRule();
        $auth->add($splitProfitLeadSoldRule);

        $leadSplitProfitPermission = $auth->createPermission($this->leadSplitProfit);
        $leadSplitProfitPermission->description = 'Lead split profit';
        $leadSplitProfitPermission->ruleName = $splitProfitLeadSoldRule->name;
        $auth->add($leadSplitProfitPermission);

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/lead/split-profit') {
                    $this->systemRoles[] = $role->name;
                }
            }
        }

        (new RbacMigrationService())->up([$this->leadSplitProfit], $this->systemRoles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/lead/split-profit') {
                    $this->systemRoles[] = $role->name;
                }
            }
        }

        (new RbacMigrationService())->down([$this->leadSplitProfit], $this->systemRoles);

        if ($permission = $auth->getPermission($this->leadSplitProfit)) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('SplitProfitLeadSoldRule')) {
            $auth->remove($rule);
        }
    }
}
