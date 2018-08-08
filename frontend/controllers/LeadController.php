<?php
namespace frontend\controllers;

use common\controllers\DefaultController;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
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
class LeadController extends DefaultController
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
                        'actions' => ['queue', 'quote'],
                        'allow' => true,
                        'roles' => ['agent'],
                        'matchCallback' => function ($rule, $action) {
                            $type = Yii::$app->request->get('type');
                            return in_array($type, Lead::getLeadQueueType());
                        },
                    ],
                    [
                        'actions' => ['create', 'add-comment'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ]
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (in_array($action->id, ['create', 'quote'])) {
                Yii::$app->setLayoutPath('@app/views/layouts');
                $this->layout = 'sale';
            }
            return true;
        }

        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return parent::actions();
    }

    public function actionGetAirport($term)
    {
        return parent::actionGetAirport($term);
    }

    public function actionQueue($type)
    {
        $this->view->title = sprintf('Leads - %s Queue', ucfirst($type));

        return $this->render('queue');
    }

    public function actionAddComment($type, $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = null;
        if ($type == 'email') {
            $model = ClientEmail::findOne(['id' => $id]);
        } elseif ($type == 'phone') {
            $model = ClientPhone::findOne(['id' => $id]);
        }
        if ($model !== null && Yii::$app->request->isPost) {
            /**
             * @var $model ClientEmail|ClientPhone
             */
            $attr = Yii::$app->request->post();
            $model->comments = $attr['comment'];
            $model->save();
            return [
                'error' => $model->getErrors(),
                'success' => !$model->hasErrors()
            ];
        }
        return null;
    }

    public function actionQuote($type, $id)
    {
        $this->view->title = sprintf('Processing Lead - %s Queue', ucfirst($type));

        $lead = Lead::findOne(['id' => $id]);

        if ($lead !== null) {
            $leadForm = new LeadForm($lead);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $data = [
                    'load' => false,
                    'errors' => []
                ];
                if ($leadForm->loadModels(Yii::$app->request->post())) {
                    $data['load'] = true;
                    $data['errors'] = ActiveForm::validate($leadForm);
                }

                $errors = [];
                if (empty($data['errors']) && $data['load'] && $leadForm->save($errors)) {
                    return $this->redirect([
                        'quote',
                        'type' => 'processing',
                        'id' => $leadForm->getLead()->id
                    ]);
                }

                if (!empty($errors)) {
                    $data['errors'] = $errors;
                }

                return $data;
            }

            return $this->render('lead', [
                'leadForm' => $leadForm
            ]);
        }
        throw new UnauthorizedHttpException('Not found lead by ID: ' . $id);
    }

    public function actionCreate()
    {
        $this->view->title = sprintf('Create Lead');

        $leadForm = new LeadForm(null);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = [
                'load' => false,
                'errors' => []
            ];
            if ($leadForm->loadModels(Yii::$app->request->post())) {
                $data['load'] = true;
                $data['errors'] = ActiveForm::validate($leadForm);
            }

            $errors = [];
            if (empty($data['errors']) && $data['load'] && $leadForm->save($errors)) {
                return $this->redirect([
                    'quote',
                    'type' => 'processing',
                    'id' => $leadForm->getLead()->id
                ]);
            }

            if (!empty($errors)) {
                $data['errors'] = $errors;
            }

            return $data;
        }

        return $this->render('lead', [
            'leadForm' => $leadForm
        ]);
    }
}
