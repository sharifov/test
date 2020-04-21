<?php

use yii\db\Migration;

/**
 * Class m190621_093542_delete_permissions_lead_itinereray
 */
class m190621_093542_delete_permissions_lead_itinerary extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadItineraryValidate = $auth->getPermission('/lead-itinerary/validate')) {
            $auth->remove($leadItineraryValidate);
        }
        if ($leadItineraryViewEditForm = $auth->getPermission('/lead-itinerary/view-edit-form')) {
            $auth->remove($leadItineraryViewEditForm);
        }
        if ($leadItineraryEdit = $auth->getPermission('/lead-itinerary/edit')) {
            $auth->remove($leadItineraryEdit);
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
    }

}
