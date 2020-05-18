<?php

use yii\db\Migration;

/**
 * Class m190816_082358_add_permissions_to_cases_q_counters
 */
class m190816_082358_add_permissions_to_cases_q_counters extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $auth = Yii::$app->authManager;

        $permission = $auth->getPermission('/cases-q/*');
        $auth->remove($permission);

        $permissions = ['/cases-q/inbox', '/cases-q/followup', '/cases-q/processing', '/cases-q/solved', '/cases-q/trash'];
        foreach ($permissions as $per) {
            $permission = $auth->createPermission($per);
            $auth->add($permission);
        }
        foreach (['admin', 'sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            foreach ($permissions as $per) {
                $permission = $auth->getPermission($per);
                $auth->addChild($role, $permission);
            }
        }

        $permission = $auth->createPermission('/cases-q/pending');
        $auth->add($permission);
        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $permission);

        $permission = $auth->createPermission('/cases-q-counters/get-q-count');
        $auth->add($permission);
        foreach (['admin', 'sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            try {
                $auth->addChild($role, $permission);
            } catch (\Throwable $e) {
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $permissions = ['/cases-q/pending', '/cases-q/inbox', '/cases-q/followup', '/cases-q/processing', '/cases-q/solved', '/cases-q/trash', '/cases-q-counters/get-q-count'];
        foreach ($permissions as $item) {
            $permission = $auth->getPermission($item);
            $auth->remove($permission);
        }

        $permission = $auth->createPermission('/cases-q/*');
        $auth->add($permission);
        foreach (['admin', 'sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            $auth->addChild($role, $permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
