<?php

use modules\objectTask\src\scenarios\NoAnswer;
use yii\db\Migration;

/**
 * Class m220906_035253_add_new_parameter_to_no_answer_scenario
 */
class m220906_035253_add_new_parameter_to_no_answer_scenario extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $scenarios = \modules\objectTask\src\entities\ObjectTaskScenario::find()
            ->where([
                'ots_key' => NoAnswer::KEY,
            ])
            ->all();

        if (!$scenarios) {
            return;
        }

        foreach ($scenarios as $scenario) {
            $json = $scenario->ots_data_json;
            $json[NoAnswer::PARAMETER_ANSWER_NOTIFICATION] = [
                NoAnswer::PARAMETER_ANSWER_NOTIFICATION_ROLES => ['supervision'],
                NoAnswer::PARAMETER_ANSWER_NOTIFICATION_TITLE => 'Received an email response from a client from no answer',
                NoAnswer::PARAMETER_ANSWER_NOTIFICATION_DESCRIPTION => 'Client {{client.last_name}} {{client.first_name}} replied to auto follow up, lead {{id}}',
            ];
            $scenario->ots_data_json = $json;
            $scenario->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $scenarios = \modules\objectTask\src\entities\ObjectTaskScenario::find()
            ->where([
                'ots_key' => NoAnswer::KEY,
            ])
            ->all();

        if (!$scenarios) {
            return;
        }

        foreach ($scenarios as $scenario) {
            $json = $scenario->ots_data_json;
            unset($json[NoAnswer::PARAMETER_ANSWER_NOTIFICATION]);
            $scenario->ots_data_json = $json;
            $scenario->save();
        }
    }
}
