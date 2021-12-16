<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210916_112940_add_new_abac_premission_for_getting_client_info_for_phone_widget
 */
class m210916_112940_add_new_abac_premission_for_getting_client_info_for_phone_widget extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.dt.year == 2021)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_dt_year","field":"env.dt.year","type":"integer","input":"number","operator":"==","value":2021}],"valid":true}',
            'ap_object' => 'client/client/act/ajax-get-info-json',
            'ap_action' => '(read)',
            'ap_action_json' => "[\"read\"]",
            'ap_effect' => 1,
            'ap_title' => 'Get client info in json data for Phone Widget',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'client/client/act/ajax-get-info-json',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
