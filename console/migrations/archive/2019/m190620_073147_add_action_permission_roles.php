<?php

use yii\db\Migration;

/**
 * Class m190620_073147_add_action_permission_roles
 */
class m190620_073147_add_action_permission_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');
        $agent = $auth->getRole('agent');
        $supervision = $auth->getRole('supervision');

        $airPortController = $auth->createPermission('/airport/get-list');
        $auth->add($airPortController);
        $auth->addChild($admin, $airPortController);
        $auth->addChild($agent, $airPortController);
        $auth->addChild($supervision, $airPortController);

        $validateLeadCreate = $auth->createPermission('/lead/validate-lead-create');
        $auth->add($validateLeadCreate);

        if ($leadCreate = $auth->getPermission('/lead/create')) {
            $auth->remove($leadCreate);
        }
        $leadCreate = $auth->createPermission('/lead/create');
        $auth->add($leadCreate);
        $createLeadPermission = $auth->getPermission('createLead');
        $auth->addChild($createLeadPermission, $leadCreate);
        $auth->addChild($createLeadPermission, $validateLeadCreate);

        $leadItineraryValidate = $auth->createPermission('/lead-itinerary/validate');
        $leadItineraryViewEditForm = $auth->createPermission('/lead-itinerary/view-edit-form');
        $leadItineraryEdit = $auth->createPermission('/lead-itinerary/edit');
        $auth->add($leadItineraryValidate);
        $auth->add($leadItineraryViewEditForm);
        $auth->add($leadItineraryEdit);
        $updateLead = $auth->getPermission('updateLead');
        $auth->addChild($updateLead, $leadItineraryValidate);
        $auth->addChild($updateLead, $leadItineraryViewEditForm);
        $auth->addChild($updateLead, $leadItineraryEdit);

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

        if ($leadCreate = $auth->getPermission('/lead/create')) {
            $auth->remove($leadCreate);
        }

        $airPortController = $auth->getPermission('/airport/get-list');
        $auth->remove($airPortController);

        $validateLeadCreate = $auth->getPermission('/lead/validate-lead-create');
        $auth->remove($validateLeadCreate);

        $leadItineraryValidate = $auth->getPermission('/lead-itinerary/validate');
        $leadItineraryViewEditForm = $auth->getPermission('/lead-itinerary/view-edit-form');
        $leadItineraryEdit = $auth->getPermission('/lead-itinerary/edit');
        $auth->remove($leadItineraryValidate);
        $auth->remove($leadItineraryViewEditForm);
        $auth->remove($leadItineraryEdit);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
