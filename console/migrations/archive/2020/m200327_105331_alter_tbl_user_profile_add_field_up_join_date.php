<?php

use common\models\UserProfile;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use \yii\db\Exception;
use yii\db\Migration;

/**
 * Class m200327_105331_alter_tbl_user_profile_add_field_up_join_date
 */
class m200327_105331_alter_tbl_user_profile_add_field_up_join_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

		$this->addColumn('{{%user_profile}}', 'up_join_date', $this->date());

		$users = UserProfile::find()->all();

		$db = Yii::$app->db;

		$command = $db->createCommand();

		foreach ($users as $user) {
			$command->update(UserProfile::tableName(),
				['up_join_date' => date('Y-m-d', strtotime($user->upUser->created_at))],
				['up_user_id' => $user->up_user_id]
			)->execute();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%user_profile}}', 'up_join_date');
    }
}
