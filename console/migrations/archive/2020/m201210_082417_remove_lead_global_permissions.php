<?php

use yii\db\Migration;

/**
 * Class m201210_082417_remove_lead_global_permissions
 */
class m201210_082417_remove_lead_global_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($isOwnerMyGroup = $auth->getPermission('global/lead/isOwner')) {
            foreach ($auth->getChildren('global/lead/isOwner') as $child) {
                $auth->removeChild($isOwnerMyGroup, $child);
            }
        }

        if ($isOwnerMyGroup = $auth->getPermission('global/lead/isEmptyOwner')) {
            foreach ($auth->getChildren('global/lead/isEmptyOwner') as $child) {
                $auth->removeChild($isOwnerMyGroup, $child);
            }
        }

        if ($isOwnerMyGroup = $auth->getPermission('global/lead/isOwnerMyGroup')) {
            foreach ($auth->getChildren('global/lead/isOwnerMyGroup') as $child) {
                $auth->removeChild($isOwnerMyGroup, $child);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
