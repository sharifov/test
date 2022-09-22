<?php

namespace frontend\controllers;

use common\models\Lead;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\quoteAward\src\forms\AwardQuoteForm;
use modules\quoteAward\src\forms\ImportGdsDumpForm;
use modules\quoteAward\src\services\QuoteFlightService;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\services\parsingDump\ReservationService;
use src\services\quote\addQuote\guard\GdsByQuoteGuard;
use Yii;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
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
        $this->quoteFlightService = $quoteFlightService;
        parent::__construct($id, $module, $config);
    }

    public function actionCreate($gid): ?string
    {
        $lead = Lead::find()->andWhere(['gid' => $gid])->limit(1)->one();

        if ($lead !== null) {
            $form = new AwardQuoteForm($lead, [], [], []);
            $form->load(\Yii::$app->request->post());

            return $this->render('create', [
                'model' => $form,
                'lead' => $lead,
            ]);
        }
        throw new NotFoundHttpException('Lead not Found');
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
                'tab' => $tabId,
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

    public function actionImportGdsDump($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        $tabId = (int)\Yii::$app->request->post('tab', 0);
        $data = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && $lead) {
            if (Yii::$app->request->isGet) {
                $form = new ImportGdsDumpForm();
                $form->tripId = Yii::$app->request->get('tripId', 0);
                return $this->renderAjax('parts/_import_gds_dump', [
                    'model' => $form,
                ]);
            }
            $response = [];
            try {
                if (Yii::$app->request->isPost) {
                    $form = new ImportGdsDumpForm();
                    $form->load($data);
                    $trips = $this->quoteFlightService->importGdsDump($form);

                    $formAward = new AwardQuoteForm(
                        $lead,
                        \Yii::$app->request->post('FlightAwardQuoteForm', []),
                        \Yii::$app->request->post('SegmentAwardQuoteForm', []),
                        \Yii::$app->request->post('PriceListAwardQuoteForm', [])
                    );
                    $formAward->loadGdsSegments($trips, (int)$form->tripId);

                    $response['flight'] = $this->renderAjax('parts/_flights', [
                        'model' => $formAward,
                        'lead' => $lead,
                        'tab' => $tabId,
                    ]);
                    $response['status'] = 'success';
                }
            } catch (\RuntimeException | \DomainException $exception) {
                $response['message'] = VarDumper::dumpAsString($exception->getMessage());
                Yii::warning(AppHelper::throwableLog($exception), 'QuoteAwardController:actionImportGdsDump:exception');
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'QuoteAwardController:actionImportGdsDump:throwable');
                $response['message'] = 'Internal Server Error';
            }

            return $this->asJson($response);
        }

        throw new BadRequestHttpException();
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
