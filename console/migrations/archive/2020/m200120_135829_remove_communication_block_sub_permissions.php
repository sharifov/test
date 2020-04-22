<?php

use yii\db\Migration;

/**
 * Class m200120_135829_remove_communication_block_sub_permissions
 */
class m200120_135829_remove_communication_block_sub_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewCommunicationBlockCommonGroup = $auth->getPermission('lead/view_CommunicationBlockCommonGroup')) {
            $auth->remove($leadViewCommunicationBlockCommonGroup);
        }

        if ($leadViewCommunicationBlockCommonGroupRule = $auth->getRule('lead/view_CommunicationBlockCommonGroupRule')) {
            $auth->remove($leadViewCommunicationBlockCommonGroupRule);
        }

        if ($leadViewCommunicationBlockEmptyOwner = $auth->getPermission('lead/view_CommunicationBlockEmptyOwner')) {
            $auth->remove($leadViewCommunicationBlockEmptyOwner);
        }

        if ($leadViewCommunicationBlockEmptyOwnerRule = $auth->getRule('lead/view_CommunicationBlockEmptyOwnerRule')) {
            $auth->remove($leadViewCommunicationBlockEmptyOwnerRule);
        }

        if ($leadViewCommunicationBlockIsOwner = $auth->getPermission('lead/view_CommunicationBlockIsOwner')) {
            $auth->remove($leadViewCommunicationBlockIsOwner);
        }

        if ($leadViewCommunicationBlockIsOwnerRule = $auth->getRule('lead/view_CommunicationBlockIsOwnerRule')) {
            $auth->remove($leadViewCommunicationBlockIsOwnerRule);
        }

        if ($leadViewCommunicationBlock = $auth->getPermission('lead/view_CommunicationBlock')) {
            if ($isOwner = $auth->getPermission('global/lead/isOwner')) {
                $auth->addChild($isOwner, $leadViewCommunicationBlock);
            }
            if ($isEmptyOwner = $auth->getPermission('global/lead/isEmptyOwner')) {
                $auth->addChild($isEmptyOwner, $leadViewCommunicationBlock);
            }
            if ($isOwnerMyGroup = $auth->getPermission('global/lead/isOwnerMyGroup')) {
                $auth->addChild($isOwnerMyGroup, $leadViewCommunicationBlock);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
