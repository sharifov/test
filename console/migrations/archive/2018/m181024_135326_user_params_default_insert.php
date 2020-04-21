<?php

use yii\db\Migration;

/**
 * Class m181024_135326_user_params_default_insert
 */
class m181024_135326_user_params_default_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $users = \common\models\Employee::find()->all();
        if($users){
            foreach ($users as $user){
                if(!$user->userParams){
                    $this->insert('{{%user_params}}', [
                        'up_user_id' => $user->id,
                        'up_commission_percent' => 10,
                        'up_base_amount' => 200,
                        'up_bonus_active' => true,
                        'up_work_start_tm' => '18:00',
                        'up_work_minutes' => 480,
                    ]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%user_params}}');
    }
}
