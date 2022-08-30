<?php

use yii\db\Migration;

/**
 * Class m220825_092026_add_new_parameter_to_sendEmailWithQuotes_command
 */
class m220825_092026_add_new_parameter_to_sendEmailWithQuotes_command extends Migration
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
            'quoteTypes' => ['best'],
            'communicationData' => [
                'day' => 1
            ],
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
                        $this->getCommandForDay(3)
                    ],
                    5 => [
                        $this->getCommandForDay(5)
                    ],
                    7 => [
                        $this->getCommandForDay(7)
                    ],
                    9 => [
                        $this->getCommandForDay(9)
                    ],
                    11 => [
                        $this->getCommandForDay(11)
                    ],
                ],
            ],
        ], [
            'ots_key' => \modules\objectTask\src\scenarios\NoAnswer::KEY,
        ]);
    }

    private function getCommandForDay(int $day): array
    {
        $command = self::COMMAND_SEND_EMAIL_WITH_QUOTES;
        $command['config']['communicationData']['day'] = $day;

        return $command;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220825_092026_add_new_parameter_to_sendEmailWithQuotes_command cannot be reverted.\n";
    }
}
