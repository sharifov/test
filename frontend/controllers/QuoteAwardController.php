<?php

namespace frontend\controllers;

use common\models\Lead;
use modules\quoteAward\src\forms\AwardQuoteForm;
use modules\quoteAward\src\services\QuoteFlightService;
use src\forms\CompositeFormHelper;
use Yii;
use yii\helpers\Html;
use yii\web\Response;

class QuoteAwardController extends FController
{
    private QuoteFlightService $quoteFlightService;

    public function __construct(
        $id,
        $module,
        QuoteFlightService $quoteFlightService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->quoteFlightService = $quoteFlightService;
    }

    public function actionCreate($leadId): ?string
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $form = new AwardQuoteForm($lead, [], [], []);
        $form->load(\Yii::$app->request->post());

        if ($lead !== null) {
            return $this->renderAjax('_quote', [
                'model' => $form,
                'lead' => $lead,
            ]);
        }
        return null;
    }

    public function actionUpdate($leadId, $type = null): ?string
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $tabId = (int)\Yii::$app->request->post('tab', 0);
        if ($lead !== null) {
            $form = new AwardQuoteForm(
                $lead,
                \Yii::$app->request->post('FlightAwardQuoteForm', []),
                \Yii::$app->request->post('SegmentAwardQuoteForm', []),
                \Yii::$app->request->post('PriceListAwardQuoteForm', [])
            );
            $form->load(\Yii::$app->request->post());

            if (!empty($type)) {
                $removeIndex = (int)\Yii::$app->request->post('index', 0);
                if ($type === AwardQuoteForm::REQUEST_FLIGHT) {
                    if ($removeIndex) {
                        $form->removeFlight($removeIndex);
                    } else {
                        $form->addFlight();
                    }
                }

                if ($type === AwardQuoteForm::REQUEST_SEGMENT) {
                    if ($removeIndex) {
                        $form->removeSegment($removeIndex);
                    } else {
                        $tripId = (int)\Yii::$app->request->post('tripId', 0);
                        $form->addSegment($tripId);
                    }
                }

                if ($type === AwardQuoteForm::REQUEST_TRIP) {
                    if ($removeIndex) {
                        $form->removeTrip($removeIndex);
                    }
                }
            }
            return $this->renderAjax('parts/_flights', [
                'model' => $form,
                'lead' => $lead,
                'tab' => $tabId
            ]);
        }
        return null;
    }

    public function actionCalcPrice($leadId): \yii\web\Response
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $prices = [];
        $refresh = (bool)\Yii::$app->request->get('refresh', false);
        if ($lead !== null) {
            $form = new AwardQuoteForm(
                $lead,
                \Yii::$app->request->post('FlightAwardQuoteForm', []),
                \Yii::$app->request->post('SegmentAwardQuoteForm', []),
                \Yii::$app->request->post('PriceListAwardQuoteForm', [])
            );
            $form->load(\Yii::$app->request->post());

            foreach ($form->flights as $flight) {
                foreach ($flight->prices as $key => $item) {
                    $item->calculatePrice((bool)$form->checkPayment, $flight, $refresh);
                    $prices[Html::getInputId($item, '[' . $flight->id . '-' . $key . ']mark_up')] = $item->mark_up;
                    $prices[Html::getInputId($item, '[' . $flight->id . '-' . $key . ']selling')] = $item->selling;
                    $prices[Html::getInputId($item, '[' . $flight->id . '-' . $key . ']net')] = $item->net;
                    $prices[Html::getInputId($item, '[' . $flight->id . '-' . $key . ']fare')] = $item->fare;
                    $prices[Html::getInputId($item, '[' . $flight->id . '-' . $key . ']taxes')] = $item->taxes;
                    $prices[Html::getInputId($item, '[' . $flight->id . '-' . $key . ']miles')] = $item->miles;
                    $prices[Html::getInputId($item, '[' . $flight->id . '-' . $key . ']oldParams')] = $item->oldParams;
                }

                foreach ($flight->getTotalPrice() as $attribute => $totalPrice) {
                    $prices[Html::getInputId($flight, '[' . $flight->id . ']' . $attribute)] = $totalPrice;
                }
            }

            foreach ($form->getTotalPrice() as $attribute => $totalPrice) {
                $prices[Html::getInputId($form, $attribute)] = $totalPrice;
            }
        }
        return $this->asJson($prices);
    }

    public function actionSave($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($lead !== null) {
            $form = new AwardQuoteForm(
                $lead,
                \Yii::$app->request->post('FlightAwardQuoteForm', []),
                \Yii::$app->request->post('SegmentAwardQuoteForm', []),
                \Yii::$app->request->post('PriceListAwardQuoteForm', [])
            );
            if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
                $this->quoteFlightService->save($form);
                return ['success' => true];
            }

            return $form;
        }

        return ['success' => false];
    }
}
