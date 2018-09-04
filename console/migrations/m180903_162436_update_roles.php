<?php

use yii\db\Migration;

/**
 * Class m180903_162436_update_roles
 */
class m180903_162436_update_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();


        //$auth = Yii::$app->authManager;

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

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180903_162436_update_roles cannot be reverted.\n";

        return false;
    }
    */
}
