<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220518_213021_add_user_update_abac_default_policy
 */
class m220518_213021_add_user_update_abac_default_policy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $superAdminPolicyExist = AbacPolicy::find()
            ->andWhere(['ap_subject' => '(r.sub.targetUserUsername == "superadmin") && (r.sub.env.user.username != "superadmin")'])
            ->andWhere(['ap_object' => 'user/user/form/user_update'])
            ->andWhere(['ap_action' => '(view)|(edit)'])
            ->andWhere(['ap_sort_order' => 1])
            ->andWhere(['ap_effect' => 0])
            ->exists();
        if (!$superAdminPolicyExist) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '(r.sub.targetUserUsername == "superadmin") && (r.sub.env.user.username != "superadmin")',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"user/user/targetUserUsername","field":"targetUserUsername","type":"string","input":"text","operator":"==","value":"superadmin"},{"id":"env_username","field":"env.user.username","type":"string","input":"text","operator":"!=","value":"superadmin"}],"valid":true}',
                'ap_object' => 'user/user/form/user_update',
                'ap_action' => '(view)|(edit)',
                'ap_action_json' => '["view","edit"]',
                'ap_effect' => 0,
                'ap_title' => 'Default: No-one can View/Edit Superadmin role beside Superadmin',
                'ap_sort_order' => 1,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        $adminPolicyExist = AbacPolicy::find()
            ->andWhere(['ap_subject' => '("admin" in r.sub.targetUserRoles) && ((r.sub.env.user.username != "superadmin") && (r.sub.targetUserIsSameUser == false))'])
            ->andWhere(['ap_object' => 'user/user/form/user_update'])
            ->andWhere(['ap_action' => '(edit)'])
            ->andWhere(['ap_sort_order' => 1])
            ->andWhere(['ap_effect' => 0])
            ->exists();
        if (!$adminPolicyExist) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.targetUserRoles) && ((r.sub.env.user.username != "superadmin") && (r.sub.targetUserIsSameUser == false))',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"user/user/targetUserRoles","field":"targetUserRoles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"env_username","field":"env.user.username","type":"string","input":"text","operator":"!=","value":"superadmin"},{"id":"user/user/targetUserIsSameUser","field":"targetUserIsSameUser","type":"boolean","input":"radio","operator":"==","value":false}]}],"valid":true}',
                'ap_object' => 'user/user/form/user_update',
                'ap_action' => '(edit)',
                'ap_action_json' => '["edit"]',
                'ap_effect' => 0,
                'ap_title' => 'Default: No-one can edit Administrator role beside the user and Superadmin',
                'ap_sort_order' => 1,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        $adminAllowAnyUserPolicyExist = AbacPolicy::find()
            ->andWhere(['ap_subject' => '("admin" in r.sub.env.user.roles)'])
            ->andWhere(['ap_object' => 'user/user/form/user_update'])
            ->andWhere(['ap_action' => '(view)|(edit)'])
            ->andWhere(['ap_sort_order' => 50])
            ->andWhere(['ap_effect' => 1])
            ->exists();
        if (!$adminAllowAnyUserPolicyExist) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'user/user/form/user_update',
                'ap_action' => '(view)|(edit)',
                'ap_action_json' => '["view","edit"]',
                'ap_effect' => 1,
                'ap_title' => 'Default: Admin can view/edit any user',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }
        if (!$superAdminPolicyExist || !$adminPolicyExist || !$adminAllowAnyUserPolicyExist) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        AbacPolicy::deleteAll(
            'ap_subject =:ap_subject and ap_object =:ap_object and ap_action =:ap_action and ap_effect =:ap_effect and ap_sort_order =:ap_sort_order',
            [
                ':ap_subject' => '(r.sub.targetUserUsername == "superadmin") && (r.sub.env.user.username != "superadmin")',
                ':ap_object' => 'user/user/form/user_update',
                ':ap_action' => '(view)|(edit)',
                ':ap_effect' => 0,
                ':ap_sort_order' => 1,
            ]
        );
        AbacPolicy::deleteAll(
            'ap_subject =:ap_subject and ap_object =:ap_object and ap_action =:ap_action and ap_effect =:ap_effect and ap_sort_order =:ap_sort_order',
            [
                ':ap_subject' => '("admin" in r.sub.targetUserRoles) && ((r.sub.env.user.username != "superadmin") && (r.sub.targetUserIsSameUser == false))',
                ':ap_object' => 'user/user/form/user_update',
                ':ap_action' => '(edit)',
                ':ap_effect' => 0,
                ':ap_sort_order' => 1
            ]
        );
        AbacPolicy::deleteAll(
            'ap_subject =:ap_subject and ap_object =:ap_object and ap_action =:ap_action and ap_effect =:ap_effect and ap_sort_order =:ap_sort_order',
            [
                ':ap_subject' => '("admin" in r.sub.env.user.roles)',
                ':ap_object' => 'user/user/form/user_update',
                ':ap_action' => '(view)|(edit)',
                ':ap_effect' => true,
                ':ap_sort_order' => 50
            ]
        );
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
