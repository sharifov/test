<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;
use common\models\DbDataSensitiveDictionary;

/**
 * Handles the creation of table `{{%data_sensitive}}`.
 */
class m220607_053422_create_db_data_sensitive_table extends Migration
{
    private const DEFAULT_KEY = 'view';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%db_data_sensitive}}', [
            'dda_id' => $this->primaryKey(),
            'dda_key' => $this->string(50)->notNull()->unique(),
            'dda_name' => $this->string(50)->notNull(),
            'dda_source' => $this->json(),
            'dda_created_dt' => $this->dateTime(),
            'dda_updated_dt' => $this->dateTime(),
            'dda_created_user_id' => $this->integer(),
            'dda_updated_user_id' => $this->integer(),
        ]);
        $this->addForeignKey('FK-db_data_sensitive-dda_created_user_id', '{{%db_data_sensitive}}', 'dda_created_user_id', '{{%employees}}', 'id', 'SET NULL');
        $this->addForeignKey('FK-db_data_sensitive-dda_updated_user_id', '{{%db_data_sensitive}}', 'dda_updated_user_id', '{{%employees}}', 'id', 'SET NULL');

        $this->insert(
            '{{%db_data_sensitive}}',
            [
                'dda_key' => self::DEFAULT_KEY,
                'dda_name' => 'Default',
                'dda_source' =>  JsonHelper::encode(DbDataSensitiveDictionary::SOURCE),
                'dda_created_dt' => date('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%db_data_sensitive}}');
    }
}
