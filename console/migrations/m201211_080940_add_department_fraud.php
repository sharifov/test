<?php

use common\models\Department;
use yii\db\Migration;

/**
 * Class m201211_080940_add_department_fraud
 */
class m201211_080940_add_department_fraud extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%department}}', [
            'dep_id'             => Department::DEPARTMENT_FRAUD_PREVENTION,
            'dep_key'            => 'fraud_prevention',
            'dep_name'           => 'Fraud prevention',
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
            'dep_id' => Department::DEPARTMENT_FRAUD_PREVENTION
        ]);
    }
}
