<?php

namespace frontend\controllers;

use sales\forms\CompositeFormAjaxValidate;
use sales\forms\lead\ItineraryForm;
use sales\repositories\lead\LeadRepository;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use \yii2mod\rbac\filters\AccessControl;

class LeadValidatorController extends FController
{
    private $leads;

    public function __construct($id, $module, LeadRepository $leads, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leads = $leads;
    }

    public function behaviors() : array
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'edit-itinerary'
                        ],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'bootstrap' => [
                'class' => ContentNegotiator::class,
                'only' => ['edit-itinerary'],  // in a controller
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['edit-itinerary']
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionEditItinerary() : array
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->leads->get($id);
        $form = new ItineraryForm($lead);
        $form->load(Yii::$app->request->post());
        return CompositeFormAjaxValidate::validate($form);
    }
}
