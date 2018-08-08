<?php
namespace frontend\controllers;

use common\controllers\DefaultController;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\Quote;
use common\models\QuotePrice;
use frontend\models\LeadForm;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\widgets\ActiveForm;

/**
 * Site controller
 */
class QuoteController extends DefaultController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'save', 'send', 'decline'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ]
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return parent::actions();
    }

    public function actionSave($save = false)
    {
        $save = (bool)$save;
        $response = [
            'success' => false,
            'errors' => [],
            'itinerary' => [],
            'save' => $save,
        ];
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post();
            if (isset($attr['Quote'])) {
                $quote = empty($attr['Quote']['id'])
                    ? new Quote()
                    : Quote::findOne(['id' => $attr['Quote']['id']]);
                if ($quote !== null) {
                    $quote->attributes = $attr['Quote'];
                    $lead = Lead::findOne(['id' => $quote->lead_id]);
                    if (isset($attr['QuotePrice']) && $lead !== null) {
                        if ($save) {
                            $quote->validate();
                            $itinerary = $quote::createDump($quote->itinerary);
                            $quote->reservation_dump = str_replace('&nbsp;', ' ', implode("\n", $itinerary));
                            $quote->save();
                            foreach ($attr['QuotePrice'] as $quotePrice) {
                                $price = empty($quotePrice['id'])
                                    ? new QuotePrice()
                                    : QuotePrice::findOne(['id' => $quotePrice['id']]);
                                if ($price !== null) {
                                    $price->attributes = $quotePrice;
                                    $price->quote_id = $quote->id;
                                    $price->save();
                                }
                            }
                        }
                        $response['success'] = $quote->validate();
                        $response['itinerary'] = $quote::createDump($quote->itinerary);
                        $response['errors'] = $quote->getErrors();
                    }
                }
            }
        }
        return $response;
    }

    public function actionCreate($leadId, $qId)
    {
        $lead = Lead::findOne(['id' => $leadId]);

        if ($lead !== null) {
            $prices = [];
            $quote = new Quote();
            if (empty($qId)) {
                $quote->lead_id = $leadId;
                $quote->trip_type = $lead->trip_type;
                $quote->check_payment = true;
                foreach ($lead->getPaxTypes() as $type) {
                    $newQPrice = new QuotePrice();
                    $newQPrice->passenger_type = $type;
                    $prices[] = $newQPrice;
                }
            } else {
                $currentQuote = Quote::findOne(['id' => $qId]);
                if ($currentQuote !== null) {
                    $prices = $currentQuote->cloneQuote($model, $lead);
                }
            }
            return $this->renderAjax('_quote', [
                'lead' => $lead,
                'quote' => $quote,
                'prices' => $prices
            ]);
        }
        return null;
    }
}
