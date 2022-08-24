<?php

use yii\db\Migration;

/**
 * Class m220823_092429_add_default_config_for_noAnswer_scenario
 */
class m220823_092429_add_default_config_for_noAnswer_scenario extends Migration
{
    private const COMMAND_SEND_EMAIL_WITH_QUOTES = [
        'command' => 'sendEmailWithQuotes',
        'config' => [
            'templateKey' => 'offer_quote_view',
            'markup' => [
                'amount' => 0,
                'percent' => 0,
                'defaultValue' => 100,
            ],
            'quotes' => 1,
            'uniqueQuotes' => false,
            'cid' => 'CRMEMK',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%object_task_scenario}}', [
            'ots_data_json' => [
                'days' => [
                    3 => [
                        self::COMMAND_SEND_EMAIL_WITH_QUOTES
                    ],
                    5 => [
                        self::COMMAND_SEND_EMAIL_WITH_QUOTES
                    ],
                    7 => [
                        self::COMMAND_SEND_EMAIL_WITH_QUOTES
                    ],
                    9 => [
                        self::COMMAND_SEND_EMAIL_WITH_QUOTES
                    ],
                    11 => [
                        self::COMMAND_SEND_EMAIL_WITH_QUOTES
                    ],
                ],
                'allowedTime' => [
                    'hour' => 12,
                    'minute' => 0
                ],
            ],
            'ots_condition' => '(lead.project in ["arangrant"]) && (lead.status == 5) && (lead.cabin in ["P","B","F"]) && (lead.reason in ["Proper Follow Up Done (No Answer)"])',
            'ots_condition_json' => '{"condition":"AND","rules":[{"id":"noAnswer/lead.project","field":"lead.project","type":"string","input":"select","operator":"in","value":["arangrant"]},{"id":"noAnswer/lead.cabin","field":"lead.cabin","type":"string","input":"select","operator":"in","value":["P","B","F"]},{"id":"noAnswer/lead.status","field":"lead.status","type":"integer","input":"select","operator":"in","value":[5]},{"id":"noAnswer/lead.reason","field":"lead.reason","type":"string","input":"select","operator":"in","value":["Proper Follow Up Done (No Answer)"]}],"not":false,"valid":true}',
        ], [
            'ots_key' => \modules\objectTask\src\scenarios\NoAnswer::KEY,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220823_092429_add_default_config_for_noAnswer_scenario cannot be reverted.\n";
    }
}
