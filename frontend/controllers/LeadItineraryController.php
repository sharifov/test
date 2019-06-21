<?php

namespace frontend\controllers;

use common\models\Lead;
use sales\forms\CompositeFormHelper;
use sales\forms\lead\ItineraryEditForm;
use sales\services\lead\LeadManageService;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use \yii2mod\rbac\filters\AccessControl;

class LeadItineraryController extends FController
{

    /** @var LeadManageService */
    private $service;

    public function __construct($id, $module, LeadManageService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(): array
    {
        $behaviors = [
            [
                'class' => ContentNegotiator::class,
                'only' => ['validate'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['validate', 'edit']
            ],

            /*[
                'class' => AjaxFilter::class,
                'only' => ['view-edit-form']
            ],*/
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionViewEditForm(): string
    {
        $id = Yii::$app->request->get('id');
        $lead = $this->findLead($id);
        $form = new ItineraryEditForm($lead);
        $form->setEditMode();
        return $this->renderAjax('/lead/partial/_flightDetails', ['itineraryForm' => $form]);
    }

    public function actionEdit(): string
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->findLead($id);
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ItineraryEditForm',
            ['segments' => 'SegmentEditForm']
        );
        $form = new ItineraryEditForm($lead, count($data['post']['SegmentEditForm']));

        if ($form->load($data['post']) && $form->validate()) {
            try {
                $this->service->editItinerary($id, $form);
                Yii::$app->session->setFlash('success', 'Segments save.');
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $lead = $this->findLead($id);
        $form = new ItineraryEditForm($lead);
        $form->setViewMode();
        return $this->renderAjax('/lead/partial/_flightDetails', ['itineraryForm' => $form]);
    }

    public function actionValidate(): array
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->findLead($id);
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ItineraryEditForm',
            ['segments' => 'SegmentEditForm']
        );
        $form = new ItineraryEditForm($lead, count($data['post']['SegmentEditForm']));
        $form->load($data['post']);
        return CompositeFormHelper::ajaxValidate($form, $data['keys']);
    }

    /**
     * @param integer $id
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLead($id): Lead
    {
        if (($model = Lead::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
