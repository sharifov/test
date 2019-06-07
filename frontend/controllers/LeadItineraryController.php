<?php

namespace frontend\controllers;

use common\models\Lead;
use sales\forms\CompositeFormAjaxValidate;
use sales\forms\lead\ItineraryEditForm;
use sales\repositories\lead\LeadRepository;
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
    /** @var LeadRepository */
    private $leads;

    /** @var LeadManageService */
    private $service;

    public function __construct($id, $module, LeadRepository $leads, LeadManageService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leads = $leads;
        $this->service = $service;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['validation', 'edit'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'class' => ContentNegotiator::class,
                'only' => ['validation'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['validation']
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionEdit()
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->findLead($id);
        $post = Yii::$app->request->post();
        $countSegments = 0;

        if (isset($post['ItineraryEditForm']['segments'])) {
            $post['SegmentEditForm'] = $post['ItineraryEditForm']['segments'];
            unset($post['ItineraryEditForm']['segments']);
            $countSegments = count($post['SegmentEditForm']);
        }
        $form = new ItineraryEditForm($lead, $countSegments);

        if ($form->load($post) && $form->validate()) {
            try {
                $this->service->editItinerary($id, $form);
                Yii::$app->session->setFlash('success', 'Segments save.');
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        } else {
            VarDumper::dump($form->errors);die;
        }
        return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
    }

    public function actionValidation(): array
    {
        $id = Yii::$app->request->post('id');
        $lead = $this->findLead($id);
        $post = Yii::$app->request->post();
//        VarDumper::dump($post);
        $countSegments = 0;
        if (isset($post['ItineraryEditForm']['segments'])) {
            $post['SegmentEditForm'] = $post['ItineraryEditForm']['segments'];
            unset($post['ItineraryEditForm']['segments']);
            $countSegments = count($post['SegmentEditForm']);
        }
        $form = new ItineraryEditForm($lead, $countSegments);
        VarDumper::dump($form);
//        $form->segmentEditForm = [];
        VarDumper::dump($form->segmentEditForm[0]);
        die;
        $form->segmentEditForm[2] = $form->segmentEditForm[1];die;
        unset($form->segmentEditForm[1]);

        $form->load($post);

        $errors = CompositeFormAjaxValidate::validate($form);
        foreach ($errors as $key => $error) {
            $newKey = str_replace('segmenteditform', 'itineraryeditform-segments', $key);
            if ($newKey !== $key) {
                $errors[$newKey] = $errors[$key];
                unset($errors[$key]);
            }
        }
        return $errors;
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
