<?php

use yii\db\Migration;

/**
 * Class m190815_134737_create_new_roles
 */
class m190815_134737_create_new_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        //======================

        $agent = $auth->getRole('agent');
        $agent->description = 'Sale agent';
        $auth->update('agent', $agent);

        $supervision = $auth->getRole('supervision');
        $supervision->description = 'Sale supervision';
        $auth->update('supervision', $supervision);

        foreach (['/call/view2', '/notifications/view2', '/sms/view2'] as $item) {
            $permission = $auth->getPermission($item);
            $auth->removeChild($agent, $permission);
            $auth->removeChild($supervision, $permission);
        }

        //  Support

        $supAgent = $auth->createRole('sup_agent');
        $supAgent->description = 'Support agent';
        $auth->add($supAgent);

        foreach ($auth->getPermissionsByRole('agent') as $permission) {
            $leadFound = mb_stripos($permission->name, 'lead');
            $quoteFound = mb_stripos($permission->name, 'quote');
            if ($leadFound === false && $quoteFound === false) {
                $auth->addChild($supAgent, $permission);
            }
        }

        $supSupervision = $auth->createRole('sup_super');
        $supSupervision->description = 'Support supervision';
        $auth->add($supSupervision);

        foreach ($auth->getPermissionsByRole('supervision') as $permission) {
            $leadFound = mb_stripos($permission->name, 'lead');
            $quoteFound = mb_stripos($permission->name, 'quote');
            if ($leadFound === false && $quoteFound === false) {
                $auth->addChild($supSupervision, $permission);
            }
        }

        //  Exchange

        $exAgent = $auth->createRole('ex_agent');
        $exAgent->description = 'Exchange agent';
        $auth->add($exAgent);

        foreach ($auth->getPermissionsByRole('agent') as $permission) {
            $auth->addChild($exAgent, $permission);
        }

                                //   Ex. Supervision
        $exSupervision = $auth->createRole('ex_super');
        $exSupervision->description = 'Exchange supervision';
        $auth->add($exSupervision);

        foreach ($auth->getPermissionsByRole('supervision') as $permission) {
            $auth->addChild($exSupervision, $permission);
        }

        //---------

        foreach (['/case-sale/*', '/cases-q/*', '/cases-status-log/*', '/cases/*'] as $item) {
            $permission = $auth->getPermission($item);
            $auth->addChild($supAgent, $permission);
            $auth->addChild($supSupervision, $permission);
            $auth->addChild($exAgent, $permission);
            $auth->addChild($exSupervision, $permission);
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

        $agent = $auth->getRole('agent');
        $agent->description = 'Agent';
        $auth->update('agent', $agent);

        $supervision = $auth->getRole('supervision');
        $supervision->description = 'Supervision';
        $auth->update('supervision', $supervision);

        foreach(['sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            $auth->remove($role);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
