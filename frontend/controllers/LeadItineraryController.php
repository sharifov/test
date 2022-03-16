<?php

namespace frontend\controllers;

use common\models\Lead;
use src\forms\CompositeFormHelper;
use src\forms\lead\ItineraryEditForm;
use src\services\lead\LeadManageService;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
            'access' => [
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view-edit-form', 'edit', 'validate'],
                        'roles' => ['@']
                    ]
                ]
            ],
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
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionViewEditForm(): string
    {
        $id = Yii::$app->request->get('id');
        $mode = Yii::$app->request->get('mode');

        $lead = $this->findLead($id);

        if (!Yii::$app->user->can('updateLead', ['lead' => $lead])) {
            throw new ForbiddenHttpException();
        }

        $form = new ItineraryEditForm($lead);
        if ($mode !== 'view') {
            $form->setEditMode();
        }
        return $this->renderAjax(
            '/lead/partial/_flightDetails',
            [
                'itineraryForm' => $form,
                'isCreatedFlightRequest' => false,
            ]
        );
    }

    public function actionEdit(): string
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->findLead($id);
        $isCreatedFlightRequest = false;
        $beginLeadSegmentCnt = $lead->leadFlightSegmentsCount;

        if (!Yii::$app->user->can('updateLead', ['lead' => $lead])) {
            throw new ForbiddenHttpException();
        }

        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ItineraryEditForm',
            ['segments' => 'SegmentEditForm']
        );
        $form = new ItineraryEditForm($lead, count($data['post']['SegmentEditForm']));

        if ($form->load($data['post']) && $form->validate()) {
            try {
                $this->service->editItinerary($lead, $form);
                Yii::$app->session->setFlash('success', 'Segments save.');
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $currentLeadSegmentCnt = $lead->leadFlightSegmentsCount;
        if ($beginLeadSegmentCnt === 0 && $currentLeadSegmentCnt > 0) {
            $isCreatedFlightRequest = true;
        }

        $lead = $this->findLead($id);
        $form = new ItineraryEditForm($lead);
        $form->setViewMode();
        return $this->renderAjax(
            '/lead/partial/_flightDetails',
            [
                'itineraryForm' => $form,
                'isCreatedFlightRequest' => $isCreatedFlightRequest,
            ]
        );
    }

    public function actionValidate(): array
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->findLead($id);

        if (!Yii::$app->user->can('updateLead', ['lead' => $lead])) {
            throw new ForbiddenHttpException();
        }

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
