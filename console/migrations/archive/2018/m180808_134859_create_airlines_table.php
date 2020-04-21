<?php

use yii\db\Migration;

/**
 * Handles the creation of table `airlines`.
 */
class m180808_134859_create_airlines_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('airlines', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'iata' => $this->string(2)->notNull(),
            'code' => $this->string(),
            'iaco' => $this->string(),
            'countryCode' => $this->string(),
            'country' => $this->string()
        ], $tableOptions);

        $this->createTable('airports', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'city' => $this->string(),
            'country' => $this->string(),
            'countryId' => $this->string(),
            'state' => $this->string(),
            'iata' => $this->string(3)->notNull(),
            'iaco' => $this->string(),
            'latitude' => $this->string(),
            'longitude' => $this->string(),
            'timezone' => $this->string(),
            'dst' => $this->string(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('airlines');
        $this->dropTable('airports');
    }
}
