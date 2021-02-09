<?php

use sales\rbac\rules\email\view\EmailViewAddressOwnerRule;
use sales\rbac\rules\email\view\EmailViewCaseOwnerRule;
use sales\rbac\rules\email\view\EmailViewEmptyRule;
use sales\rbac\rules\email\view\EmailViewGroupRule;
use sales\rbac\rules\email\view\EmailViewLeadOwnerRule;
use sales\rbac\rules\email\view\EmailViewOwnerRule;
use yii\db\Migration;

/**
 * Class m201109_082452_add_email_view_permissions
 */
class m201109_082452_add_email_view_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $emailView = $auth->createPermission('email/view');
        $emailView->description = 'Email view';
        $auth->add($emailView);

        $emailViewAll = $auth->createPermission('email/view/all');
        $emailViewAll->description = 'Email view all';
        $auth->add($emailViewAll);
        $auth->addChild($emailViewAll, $emailView);

        $emailViewEmptyRule = new EmailViewEmptyRule();
        $auth->add($emailViewEmptyRule);
        $emailViewEmpty = $auth->createPermission('email/view/empty');
        $emailViewEmpty->description = 'Email view group';
        $emailViewEmpty->ruleName = $emailViewEmptyRule->name;
        $auth->add($emailViewEmpty);
        $auth->addChild($emailViewEmpty, $emailView);

        $emailViewOwnerRule = new EmailViewOwnerRule();
        $auth->add($emailViewOwnerRule);
        $emailViewOwner = $auth->createPermission('email/view/owner');
        $emailViewOwner->description = 'Email view owner';
        $emailViewOwner->ruleName = $emailViewOwnerRule->name;
        $auth->add($emailViewOwner);
        $auth->addChild($emailViewOwner, $emailView);

        $emailViewCaseOwnerRule = new EmailViewCaseOwnerRule();
        $auth->add($emailViewCaseOwnerRule);
        $emailViewCaseOwner = $auth->createPermission('email/view/case_owner');
        $emailViewCaseOwner->description = 'Email view group';
        $emailViewCaseOwner->ruleName = $emailViewCaseOwnerRule->name;
        $auth->add($emailViewCaseOwner);
        $auth->addChild($emailViewCaseOwner, $emailView);

        $emailViewLeadOwnerRule = new EmailViewLeadOwnerRule();
        $auth->add($emailViewLeadOwnerRule);
        $emailViewLeadOwner = $auth->createPermission('email/view/lead_owner');
        $emailViewLeadOwner->description = 'Email view group';
        $emailViewLeadOwner->ruleName = $emailViewLeadOwnerRule->name;
        $auth->add($emailViewLeadOwner);
        $auth->addChild($emailViewLeadOwner, $emailView);

        $emailViewAddressOwnerRule = new EmailViewAddressOwnerRule();
        $auth->add($emailViewAddressOwnerRule);
        $emailViewAddressOwner = $auth->createPermission('email/view/address_owner');
        $emailViewAddressOwner->description = 'Email view group';
        $emailViewAddressOwner->ruleName = $emailViewAddressOwnerRule->name;
        $auth->add($emailViewAddressOwner);
        $auth->addChild($emailViewAddressOwner, $emailView);

        $emailViewGroupRule = new EmailViewGroupRule();
        $auth->add($emailViewGroupRule);
        $emailViewGroup = $auth->createPermission('email/view/group');
        $emailViewGroup->description = 'Email view group';
        $emailViewGroup->ruleName = $emailViewGroupRule->name;
        $auth->add($emailViewGroup);
        $auth->addChild($emailViewGroup, $emailView);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'EmailViewEmptyRule',
            'EmailViewOwnerRule',
            'EmailViewCaseOwnerRule',
            'EmailViewLeadOwnerRule',
            'EmailViewAddressOwnerRule',
            'EmailViewGroupRule',
        ];

        $permissions = [
            'email/view/group',
            'email/view/address_owner',
            'email/view/lead_owner',
            'email/view/case_owner',
            'email/view/owner',
            'email/view/empty',
            'email/view/all',
            'email/view',
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
