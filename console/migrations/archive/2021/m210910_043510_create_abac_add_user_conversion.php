<?php

use frontend\helpers\JsonHelper;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m210910_043510_create_abac_add_user_conversion
 */
class m210910_043510_create_abac_add_user_conversion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($abacPolicyRead = AbacPolicy::findOne(['ap_object' => 'lead/lead/act/user-conversion', 'ap_action' => '(read)'])) {
            $subjectJson = JsonHelper::decode($abacPolicyRead->ap_subject_json);

            $qaExist = false;
            $qaSuperExist = false;
            foreach ($subjectJson['rules'] as $rule) {
                if ($rule['id'] === 'env_user_roles' && $rule['value'] === 'qa') {
                    $qaExist = true;
                }
                if ($rule['id'] === 'env_user_roles' && $rule['value'] === 'qa_super') {
                    $qaSuperExist = true;
                }
            }

            if (!$qaExist) {
                $subjectJson['rules'][] = [
                    'id' => 'env_user_roles',
                    'field' => 'env.user.roles',
                    'type' => 'string',
                    'input' => 'select',
                    'operator' => 'in_array',
                    'value' => 'qa',
                ];
            }
            if (!$qaSuperExist) {
                $subjectJson['rules'][] = [
                    'id' => 'env_user_roles',
                    'field' => 'env.user.roles',
                    'type' => 'string',
                    'input' => 'select',
                    'operator' => 'in_array',
                    'value' => 'qa_super',
                ];
            }

            $abacPolicyRead->ap_subject_json = JsonHelper::encode($subjectJson);
            $abacPolicyRead->save();
        }

        if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/act/user-conversion', 'ap_action' => '(create)'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '("admin" in r.sub.env.user.roles) || ("qa" in r.sub.env.user.roles) || ("qa_super" in r.sub.env.user.roles)',
                    'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_super"}],"valid":true}',
                    'ap_object' => 'lead/lead/act/user-conversion',
                    'ap_action' => '(create)',
                    'ap_action_json' => "[\"create\"]",
                    'ap_effect' => 1,
                    'ap_title' => 'User Conversion Create',
                    'ap_sort_order' => 50,
                    'ap_enabled' => 1,
                    'ap_created_dt' => date('Y-m-d H:i:s'),
                ]
            );
        }
        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        AbacPolicy::deleteAll(['ap_object' => 'lead/lead/act/user-conversion', 'ap_action' => '(create)']);

        if ($abacPolicyRead = AbacPolicy::findOne(['ap_object' => 'lead/lead/act/user-conversion', 'ap_action' => '(read)'])) {
            $subjectJson = JsonHelper::decode($abacPolicyRead->ap_subject_json);

            foreach ($subjectJson['rules'] as $key => $rule) {
                if ($rule['id'] === 'env_user_roles' && ($rule['value'] === 'qa' || $rule['value'] === 'qa_super')) {
                    unset($subjectJson['rules'][$key]);
                }
            }
            $abacPolicyRead->ap_subject_json = JsonHelper::encode($subjectJson);
            $abacPolicyRead->save();
        }

        Yii::$app->abac->invalidatePolicyCache();
    }
}
