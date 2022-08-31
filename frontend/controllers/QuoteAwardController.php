<?php

namespace frontend\controllers;

use common\models\Lead;
use modules\quoteAward\src\forms\AwardQuoteForm;
use modules\quoteAward\src\forms\FlightAwardQuoteForm;
use src\forms\CompositeFormHelper;

class QuoteAwardController extends FController
{
    public function actionCreate($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $form = new AwardQuoteForm($lead, [], [], []);

        if ($lead !== null) {
            return $this->renderAjax('_quote', [
                'model' => $form,
                'lead' => $lead
            ]);
        }
        return null;
    }

    public function actionFlight($leadId): ?string
    {
        $lead = Lead::findOne(['id' => $leadId]);

        $form = new AwardQuoteForm(
            $lead,
            \Yii::$app->request->post('FlightAwardQuoteForm', []),
            \Yii::$app->request->post('SegmentAwardQuoteForm', []),
            \Yii::$app->request->post('PriceListAwardQuoteForm', [])
        );

        if ($lead !== null) {
            $removeIndex = (int)\Yii::$app->request->post('index', 0);
            if ($removeIndex) {
                $form->removeFlight($removeIndex);
            } else {
                $form->addFlight();
            }

            return $this->renderAjax('parts/_flights', [
                'model' => $form,
                'lead' => $lead
            ]);
        }
        return null;
    }

    public function actionSegment($leadId): ?string
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $form = new AwardQuoteForm(
            $lead,
            \Yii::$app->request->post('FlightAwardQuoteForm', []),
            \Yii::$app->request->post('SegmentAwardQuoteForm', []),
            \Yii::$app->request->post('PriceListAwardQuoteForm', [])
        );

        if ($lead !== null) {
            $removeIndex = (int)\Yii::$app->request->post('index', 0);
            if ($removeIndex) {
                $form->removeSegment($removeIndex);
            } else {
                $form->addSegment();
            }

            return $this->renderAjax('parts/_flights', [
                'model' => $form,
                'lead' => $lead
            ]);
        }
        return null;
    }

    public function actionUpdate($leadId): ?string
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $form = new AwardQuoteForm(
            $lead,
            \Yii::$app->request->post('FlightAwardQuoteForm', []),
            \Yii::$app->request->post('SegmentAwardQuoteForm', []),
            \Yii::$app->request->post('PriceListAwardQuoteForm', [])
        );

        if ($lead !== null) {
            return $this->renderAjax('parts/_flights', [
                'model' => $form,
                'lead' => $lead
            ]);
        }
        return null;
    }
}
