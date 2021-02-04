<?php

use common\models\Employee;
use frontend\helpers\SlugHelper;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m200825_072232_add_column_nickname_client_chat_to_employees
 */
class m200825_072232_add_column_nickname_client_chat_to_employees extends Migration
{
    public function safeUp()
    {

        $this->addColumn('{{%employees}}', 'nickname_client_chat', $this->string(255));

        foreach (Employee::find()->all() as $employee) {
            /** @var Employee $employee */
            $nickname = !empty($employee->nickname) ? $employee->nickname : $employee->username;
            $employee->nickname_client_chat = SlugHelper::prepare($nickname);

            if (!$employee->save(false)) {
                echo 'Nickname client chat not saved. ' .
                    $employee->nickname_client_chat . ' : ' .
                    $employee->id . PHP_EOL;
            }
        }

        $this->alterColumn('{{%employees}}', 'nickname_client_chat', $this->string(255)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employees}}', 'nickname_client_chat');
    }
}
