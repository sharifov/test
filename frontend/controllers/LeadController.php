<?php

namespace frontend\controllers;

use common\controllers\DefaultController;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\Note;
use common\models\Reason;
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
                            if ($type == 'trash' && Yii::$app->user->identity->role == 'agent') {
                                return false;
                            }
                            return in_array($type, Lead::getLeadQueueType());
                        },
                    ],
                    [
                        'actions' => [
                            'create', 'add-comment', 'change-state', 'unassign', 'take',
                            'set-rating'
                        ],
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

    public function actionSetRating($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null &&
            $lead->status == Lead::STATUS_PROCESSING &&
            Yii::$app->request->isPost
        ) {
            $rating = Yii::$app->request->post('rating', 0);
            $lead->rating = $rating;
            $lead->save(false);
            return true;
        }
        return false;
    }

    public function actionUnassign($id)
    {
        $model = Lead::findOne([
            'id' => $id
        ]);
        if ($model !== null) {
            $reason = new Reason();
            $attr = Yii::$app->request->post($reason->formName());
            if (empty($attr)) {
                if ($attr['queue'] == 'processing') {
                    $model->status = $model::STATUS_PROCESSING;
                    $model->snooze_for = '';
                    $model->save();
                    return $this->redirect([
                        'quote',
                        'type' => 'processing',
                        'id' => $model->id
                    ]);
                } elseif ($attr['queue'] == 'reject') {
                    $model->status = $model::STATUS_REJECT;
                    $model->save();
                    return $this->redirect([
                        'queue',
                        'type' => 'trash'
                    ]);
                }
            } else {
                $reason->attributes = $attr;
                $reason->employee_id = Yii::$app->user->identity->getId();
                $reason->lead_id = $model->id;
                $reason->save();
                if ($reason->queue == 'follow-up') {
                    $model->status = $model::STATUS_FOLLOW_UP;
                    $model->employee_id = null;
                } elseif ($reason->queue == 'trash') {
                    $model->status = $model::STATUS_TRASH;
                } elseif ($reason->queue == 'snooze') {
                    $modelAttr = Yii::$app->request->post($model->formName());
                    $model->snooze_for = $modelAttr['snooze_for'];
                    $model->status = $model::STATUS_SNOOZE;
                } elseif ($reason->queue == 'return') {
                    $attrAgent = Yii::$app->request->post('agent', null);
                    if ($reason->returnToQueue == 'follow-up') {
                        $model->status = $model::STATUS_FOLLOW_UP;
                    } elseif ($attrAgent !== null) {
                        $model->employee_id = $attrAgent;
                        $model->status = $model::STATUS_ON_HOLD;
                    }
                } elseif ($reason->queue == 'processing-over') {
                    $model->status = $model::STATUS_PROCESSING;
                    $lastAgent = $model->employee->username;
                    $model->employee_id = $reason->employee_id;
                    $model->save();

                    $note = new Note();
                    $note->employee_id = Yii::$app->user->identity->getId();
                    $note->lead_id = $model->id;
                    $note->message = sprintf('Take Over in PROCESSING status.<br>Reason: %s<br>Last Agent: %s',
                        $reason->reason,
                        $lastAgent
                    );
                    $note->save();

                    return $this->redirect([
                        'quote',
                        'type' => 'processing',
                        'id' => $model->id
                    ]);
                } else {
                    $model->status = $model::STATUS_ON_HOLD;
                }
                $model->save();
            }
        }
        return $this->redirect([
            'queue',
            'type' => 'inbox'
        ]);
    }

    public function actionChangeState($id, $queue)
    {
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $reason = new Reason();
            $reason->queue = $queue;
            return $this->renderAjax('partial/_reason', [
                'reason' => $reason,
                'lead' => $lead
            ]);
        }
        return null;
    }

    public function actionTake($id)
    {
        /**
         * @var $inProcessing Lead
         * @var $model Lead
         */
        $inProcessing = Lead::find()
            ->where([
                'employee_id' => Yii::$app->user->identity->getId(),
                'status' => Lead::STATUS_PROCESSING
            ])->one();
        if ($inProcessing !== null) {
            $inProcessing->status = Lead::STATUS_ON_HOLD;
            $inProcessing->save();
            $inProcessing = null;
        }

        $model = Lead::find()
            ->where(['id' => $id])
            ->andWhere(['IN', 'status', [
                Lead::STATUS_PENDING,
                Lead::STATUS_FOLLOW_UP,
                Lead::STATUS_ON_HOLD,
                Lead::STATUS_SNOOZE
            ]])->one();

        if ($model === null) {
            $lead = Lead::findOne(['id' => $id]);
            if ($lead !== null) {
                $reason = new Reason();
                $reason->queue = 'processing-over';
                return $this->renderAjax('partial/_reason', [
                    'reason' => $reason,
                    'lead' => $lead
                ]);
            }
            return null;
        }

        $model->employee_id = Yii::$app->user->identity->getId();
        $model->status = Lead::STATUS_PROCESSING;
        $model->save();

        return $this->redirect([
            'quote',
            'type' => 'processing',
            'id' => $model->id
        ]);

    }

    public function actionQueue($type)
    {
        $this->view->title = sprintf('Leads - %s Queue', ucfirst($type));

        $searchModel = null;
        if (in_array($type, ['processing-all', 'processing', 'follow-up'])) {
            $dataProvider = [];
            foreach (array_keys(Lead::getDivs()) as $div) {
                if ($div == Lead::DIV_GRID_IN_SNOOZE && $type == 'follow-up') {
                    continue;
                }
                if ($type == 'processing-all') {
                    $searchModel = new Lead();
                    $dataProvider[$div] = Lead::search($type, $searchModel, $div);
                } else {
                    $dataProvider[$div] = Lead::search($type, null, $div);
                }
            }
        } else {
            $dataProvider = Lead::search($type);
        }

        return $this->render('queue', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
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
            if ($leadForm->getLead()->status !== Lead::STATUS_PROCESSING ||
                $leadForm->getLead()->employee_id != Yii::$app->user->identity->getId()
            ) {
                $leadForm->mode = $leadForm::VIEW_MODE;
            }
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
