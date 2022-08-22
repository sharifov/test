<?php

use yii\db\Migration;

/**
 * Class m220816_054358_add_no_answer_to_object_task_scenario_table
 */
class m220816_054358_add_no_answer_to_object_task_scenario_table extends Migration
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
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%object_task_scenario}}', [
            'ots_key' => \modules\objectTask\src\scenarios\NoAnswer::KEY,
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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%object_task_scenario}}', [
            'ots_key' => \modules\objectTask\src\scenarios\NoAnswer::KEY,
        ]);
    }
}
