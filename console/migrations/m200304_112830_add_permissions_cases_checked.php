<?php

use common\models\Employee;
use sales\rbac\rules\cases\view\CasesViewCheckedOwnerRule;
use yii\db\Migration;

/**
 * Class m200304_112830_add_permissions_cases_checked
 */
class m200304_112830_add_permissions_cases_checked extends Migration
{
    private $rolesAdmin = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private $rolesSimple = [
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_SUPPORT_SENIOR,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $checked = $auth->createPermission('cases/view_Checked');
        $checked->description = 'Cases View Checked';
        $auth->add($checked);

        foreach ($this->rolesAdmin as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $checked);
            }
        }

        $checkedOwnerRule = new CasesViewCheckedOwnerRule();
        $auth->add($checkedOwnerRule);

        $checkedOwner = $auth->createPermission('cases/view_Checked_Owner');
        $checkedOwner->description = 'Cases View Checked Owner';
        $checkedOwner->ruleName = $checkedOwnerRule->name;
        $auth->add($checkedOwner);
        $auth->addChild($checkedOwner, $checked);

        foreach ($this->rolesSimple as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $checkedOwner);
            }
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

        if ($checkedOwner = $auth->getPermission('cases/view_Checked_Owner')) {
            $auth->remove($checkedOwner);
        }

        if ($checkedOwnerRule = $auth->getRule('CasesViewCheckedOwnerRule')) {
            $auth->remove($checkedOwnerRule);
        }

        if ($checked = $auth->getPermission('cases/view_Checked')) {
            $auth->remove($checked);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
