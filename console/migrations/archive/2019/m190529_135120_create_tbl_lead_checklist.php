<?php

use yii\db\Migration;

/**
 * Class m190529_135120_create_tbl_lead_checklist
 */
class m190529_135120_create_tbl_lead_checklist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%lead_checklist_type}}', [
            'lct_id' => $this->primaryKey(),
            'lct_key' => $this->string(50)->unique()->notNull(),
            'lct_name' => $this->string(255)->notNull(),
            'lct_description' => $this->string(500),
            'lct_enabled' => $this->boolean()->defaultValue(true),
            'lct_sort_order' => $this->integer()->defaultValue(5),
            'lct_updated_dt' => $this->dateTime(),
            'lct_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('FK-lead_checklist_type_lct_updated_user_id', '{{%lead_checklist_type}}', ['lct_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createTable('{{%lead_checklist}}', [
            'lc_type_id' => $this->integer()->notNull(),
            'lc_lead_id' => $this->integer()->notNull(),
            'lc_user_id' => $this->integer()->notNull(),
            'lc_notes' => $this->string(500),
            'lc_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-lead_checklist', '{{%lead_checklist}}', ['lc_type_id', 'lc_lead_id', 'lc_user_id']);
        $this->addForeignKey('FK-lead_checklist_lc_user_id', '{{%lead_checklist}}', ['lc_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-lead_checklist_lc_lead_id', '{{%lead_checklist}}', ['lc_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-lead_checklist_lc_lct_id', '{{%lead_checklist}}', ['lc_type_id'], '{{%lead_checklist_type}}', ['lct_id'], 'CASCADE', 'CASCADE');

        $checklistTypes = [];

        $checklistTypes[0] = ['key' => 'flight_info', 'name' => 'Flight Info Checked', 'description' => 'Customer has confirmed Flight Info.', 'sort' => 1];
        $checklistTypes[1] = ['key' => 'lead_preferences', 'name' => 'Lead preferences checked', 'description' => 'Customer has been asked for preferences (Stops, Time Preference ,Total Trip Duration, Favorite Airlines, Layovers, Neighboring Airports, Budget)', 'sort' => 2];
        $checklistTypes[2] = ['key' => 'client_info', 'name' => 'Client Info Checked', 'description' => 'Customer has confirmed contact information', 'sort' => 3];
        $checklistTypes[3] = ['key' => 'urgency_created', 'name' => 'Urgency (Rush) created', 'description' => 'Did you create Urgency?', 'sort' => 4];
        $checklistTypes[4] = ['key' => 'offer_presentation', 'name' => 'Offer Presentation', 'description' => 'Did you offer any options to customer?', 'sort' => 5];

        foreach ($checklistTypes as $type) {
            $this->insert('{{%lead_checklist_type}}', [
                'lct_key' => $type['key'],
                'lct_name' => $type['name'],
                'lct_description' => $type['description'],
                'lct_sort_order' => $type['sort'],
                'lct_enabled' => true,
                'lct_updated_dt' => date('Y-m-d H:i:s'),
            ]);
        }


        $auth = \Yii::$app->authManager;

        $adminRole = $auth->getRole('admin');

        $permission = $auth->createPermission('/lead-checklist-type/*');
        $auth->add($permission);
        $auth->addChild($adminRole, $permission);

        $permission = $auth->createPermission('/lead-checklist/*');
        $auth->add($permission);
        $auth->addChild($adminRole, $permission);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        //Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_checklist_type}}');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_checklist}}');
        $this->dropTable('{{%lead_checklist_type}}');

        $auth = \Yii::$app->authManager;



        $permission = $auth->getPermission('/lead-checklist-type/*');
        if($permission) {
            $auth->remove($permission);
        }


        $permission = $auth->createPermission('/lead-checklist/*');
        if($permission) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
