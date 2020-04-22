<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%employees}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'full_name' => $this->string(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'last_activity' => $this->dateTime(),
            'acl_rules_activated' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable('{{%employee_profile}}', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'role' => $this->string()->notNull(),
            'profile_info' => $this->text()->notNull(),
            'updated_at' => $this->dateTime(),
        ], $tableOptions);
        $this->addForeignKey('fk-employee_profile-employees', 'employee_profile', 'employee_id', 'employees', 'id');

        // create rbac
        $auth = Yii::$app->authManager;

        $trainerAgents = $auth->createPermission('trainerAgents');
        $trainerAgents->description = 'Trainer agents';
        $auth->add($trainerAgents);

        $manageLeads = $auth->createPermission('manageLeads');
        $manageLeads->description = 'Manage leads';
        $auth->add($manageLeads);

        $manageAgents = $auth->createPermission('manageAgents');
        $manageAgents->description = 'Manage agents';
        $auth->add($manageAgents);


        // add "admin" and "user" role
        $coach = $auth->createRole('coach');
        $coach->description = 'Agents coach';
        $auth->add($coach);
        $auth->addChild($coach, $trainerAgents);

        $agent = $auth->createRole('agent');
        $agent->description = 'Agent';
        $auth->add($agent);
        $auth->addChild($agent, $coach);
        $auth->addChild($agent, $manageLeads);

        $supervision = $auth->createRole('supervision');
        $supervision->description = 'Supervision';
        $auth->add($supervision);
        $auth->addChild($supervision, $agent);
        $auth->addChild($supervision, $manageAgents);

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);
        $auth->addChild($admin, $supervision);

        //create leads relationship
        $this->createTable('leads', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(),
            'employee_id' => $this->integer(),
            'status' => $this->integer(),
            'uid' => $this->string(),
            'project_id' => $this->integer(),
            'source_id' => $this->integer(),
            'trip_type' => $this->string(2)->notNull(),
            'cabin' => $this->string(1)->notNull(),
            'adults' => $this->integer(1),
            'children' => $this->integer(1),
            'infants' => $this->integer(1),
            'notes_for_experts' => $this->text(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime()
        ], $tableOptions);
        $this->addForeignKey('fk-lead-employees', 'leads', 'employee_id', 'employees', 'id');

        $this->createTable('lead_preferences', [
            'id' => $this->primaryKey(),
            'lead_id' => $this->integer(),
            'notes' => $this->text(),
            'pref_language' => $this->string(),
            'pref_currency' => $this->string(),
            'pref_airline' => $this->string(),
            'number_stops' => $this->integer(),
            'clients_budget' => $this->float(),
            'market_price' => $this->float(),
        ], $tableOptions);
        $this->addForeignKey('fk-leadpref-lead', 'lead_preferences', 'lead_id', 'leads', 'id', 'CASCADE');

        $this->createTable('lead_flight_segments', [
            'id' => $this->primaryKey(),
            'lead_id' => $this->integer(),
            'origin' => $this->string(3)->notNull(),
            'destination' => $this->string(3)->notNull(),
            'departure' => $this->date()->notNull(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime(),
        ], $tableOptions);
        $this->addForeignKey('fk-leadfs-lead', 'lead_flight_segments', 'lead_id', 'leads', 'id', 'CASCADE');

        $this->createTable('clients', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(100)->notNull(),
            'middle_name' => $this->string(100),
            'last_name' => $this->string(100),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime(),
        ], $tableOptions);
        $this->addForeignKey('fk-lead-clients', 'leads', 'client_id', 'clients', 'id');

        $this->createTable('client_email', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->notNull(),
            'email' => $this->string(100)->notNull(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime(),
        ], $tableOptions);
        $this->addForeignKey('fk-clients-client_email', 'client_email', 'client_id', 'clients', 'id', 'CASCADE');

        $this->createTable('client_phone', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->notNull(),
            'phone' => $this->string(100)->notNull(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime(),
        ], $tableOptions);
        $this->addForeignKey('fk-clients-client_phone', 'client_phone', 'client_id', 'clients', 'id', 'CASCADE');


        $employee = new \common\models\Employee();
        $employee->username = 'admin';
        $employee->email = 'admin@zeit.style';
        $employee->acl_rules_activated = false;
        $employee->setPassword('admin');
        $employee->generateAuthKey();
        $employee->save(false);

        // the following three lines were added:
        $auth = \Yii::$app->authManager;
        $authorRole = $auth->getRole('admin');
        $auth->assign($authorRole, $employee->getId());
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $this->dropForeignKey('fk-employee_profile-employees', 'employee_profile');
        $this->dropForeignKey('fk-lead-employees', 'leads');
        $this->dropForeignKey('fk-lead-clients', 'leads');
        $this->dropForeignKey('fk-leadpref-lead', 'lead_preferences');
        $this->dropForeignKey('fk-leadfs-lead', 'lead_flight_segments');
        $this->dropForeignKey('fk-clients-client_email', 'client_email');
        $this->dropForeignKey('fk-clients-client_phone', 'client_phone');

        $this->dropTable('{{%client_phone}}');
        $this->dropTable('{{%client_email}}');
        $this->dropTable('{{%clients}}');
        $this->dropTable('{{%lead_flight_segments}}');
        $this->dropTable('{{%lead_preferences}}');
        $this->dropTable('{{%leads}}');
        $this->dropTable('{{%employee_profile}}');
        $this->dropTable('{{%employees}}');


    }
}
