<?php

use yii\db\Migration;

/**
 * Class m220819_040313_add_json_statement_to_object_task_scenario_no_answer
 */
class m220819_040313_add_json_statement_to_object_task_scenario_no_answer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%object_task_scenario}}',
            [
                'ots_condition_json' => '{"condition":"AND","rules":[{"id":"noAnswer/lead.project","field":"lead.project","type":"string","input":"select","operator":"==","value":["7"]},{"id":"noAnswer/lead.status","field":"lead.status","type":"integer","input":"select","operator":"==","value":5},{"id":"noAnswer/lead.reason","field":"lead.reason","type":"string","input":"select","operator":"==","value":"Didn\'t get in touch"},{"id":"noAnswer/lead.cabin","field":"lead.cabin","type":"string","input":"select","operator":"in","value":["P","B","F"]}],"not":false,"valid":true}',
                'ots_condition' => '(lead.project in ["7"]) && (lead.status == 5) && (lead.reason == "Didn\'t get in touch") && (lead.cabin in ["P","B","F"])',
            ],
            [
                'ots_key' => \modules\objectTask\src\scenarios\NoAnswer::KEY
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update(
            '{{%object_task_scenario}}',
            [
                'ots_condition_json' => null,
                'ots_condition' => null,
            ],
            [
                'ots_key' => \modules\objectTask\src\scenarios\NoAnswer::KEY
            ]
        );
    }
}
