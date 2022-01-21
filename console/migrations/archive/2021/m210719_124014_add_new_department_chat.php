<?php

use yii\db\Migration;

/**
 * Class m210719_124014_add_new_department_chat
 */
class m210719_124014_add_new_department_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%department}}', [
            'dep_id'             => \common\models\Department::DEPARTMENT_CHAT,
            'dep_key'            => 'chat',
            'dep_name'           => 'Chat',
            'dep_updated_user_id' => null,
            'dep_updated_dt'     => date('Y-m-d H:i:s'),
            'dep_params'        => '{
  "default_phone_type": "Only general",
  "object": {
    "type": "case",
    "lead": {
      "createOnCall": false,
      "createOnSms": false,
      "createOnEmail": false
    },
    "case": {
      "createOnCall": true,
      "createOnSms": false,
      "createOnEmail": false,
      "trashActiveDaysLimit": 14
    }
  }
}',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%department}}', [
            'dep_id' => \common\models\Department::DEPARTMENT_CHAT
        ]);
    }
}
