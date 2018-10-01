<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\controllers\DefaultController;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\EmployeeContactInfo;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadLog;
use common\models\local\LeadAdditionalInformation;
use common\models\Note;
use common\models\ProjectEmailTemplate;
use common\models\Reason;
use frontend\models\LeadForm;
use frontend\models\SendEmailForm;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
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
                'class' => AccessControl::class,
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
                            'set-rating', 'add-note', 'unprocessed', 'call-expert', 'send-email',
                            'check-updates', 'flow-transition', 'get-user-actions', 'add-pnr'
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

    public function actionAddPnr($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        if ($lead !== null) {
            if (Yii::$app->request->isPost) {
                $model = new LeadAdditionalInformation();
                $attr = Yii::$app->request->post($model->formName());
                if (empty($attr['pnr'])) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $errors[Html::getInputId($model, 'pnr')] = sprintf('Cannot be blank');
                    return [
                        'errors' => $errors
                    ];
                } else {
                    $lead->additionalInformationForm->pnr = $attr['pnr'];
                    $quote = $lead->getAppliedAlternativeQuotes();
                    if ($quote !== null) {
                        $quote->record_locator = $lead->additionalInformationForm->pnr;
                        $quote->save();
                    }
                    $lead->save();
                    $data = [
                        'FlightRequest' => [
                            'id' => $lead->bo_flight_id,
                            'sub_sources_id' => $lead->source_id,
                            'pnr' => $lead->additionalInformationForm->pnr
                        ]
                    ];
                    $result = BackOffice::sendRequest('lead/add-pnr', 'POST', json_encode($data));
                    if ($result['status'] != 'Success') {
                        $quote->record_locator = null;
                        $lead->additionalInformationForm->pnr = null;
                        $quote->save();
                        $lead->save();
                        Yii::$app->getSession()->setFlash('warning', sprintf(
                            'Add PNR failed! %s',
                            print_r($result['errors'], true)
                        ));
                    }
                    return $this->redirect([
                        'quote',
                        'type' => 'processing',
                        'id' => $lead->id
                    ]);
                }
            }
            return $this->renderAjax('partial/_paxInfo', [
                'lead' => $lead
            ]);
        }
        return null;
    }

    public function actionFlowTransition($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        if ($lead !== null) {
            return $this->renderAjax('partial/_flowTransition', [
                'flightRequestFlow' => $lead->getFlowTransition(),
            ]);
        }
        return null;
    }

    public function actionCheckUpdates($leadId, $lastUpdate)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'needRefresh' => false
        ];
        $model = Lead::findOne([
            'id' => $leadId
        ]);
        if ($model !== null) {
            $query = LeadLog::find()
                ->where(['lead_id' => $leadId])
                ->andWhere('created > :lastUpdate', [':lastUpdate' => $lastUpdate]);

            $logs = $query->all();
            if (count($logs)) {
                $response['logs'] = $this->renderAjax('partial/_leadLog', [
                    'logs' => $model->getLogs()
                ]);
                $response['checkUpdatesUrl'] = Url::to([
                    'lead/check-updates',
                    'leadId' => $leadId,
                    'lastUpdate' => date('Y-m-d H:i:s'),
                ]);
                $response['content'] = $this->renderAjax('partial/_updateModal');
            } else {
                $response['logs'] = '';
                $response['checkUpdatesUrl'] = Url::to([
                    'lead/check-updates',
                    'leadId' => $leadId,
                    'lastUpdate' => $lastUpdate,
                ]);
            }
            $needRefresh = $query->andWhere('employee_id <> :employee_id OR employee_id IS NULL', [
                ':employee_id' => Yii::$app->user->identity->getId()
            ])->all();

            $response['needRefresh'] = count($needRefresh);
        }

        return $response;
    }

    public function actionSendEmail($id)
    {
        /**
         * @var $lead Lead
         */

        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $preview = false;
            $sendEmailModel = new SendEmailForm();
            $sendEmailModel->employee = $lead->employee;
            $sendEmailModel->project = $lead->project;
            $sellerContactInfo = EmployeeContactInfo::findOne([
                'employee_id' => $sendEmailModel->employee->id,
                'project_id' => $sendEmailModel->project->id
            ]);
            $templates = ProjectEmailTemplate::getTypesForSellers();
            if (Yii::$app->request->isAjax) {
                $sendEmailModel->type = Yii::$app->request->get('type');
                $template = $sendEmailModel->getTemplate();
                if (Yii::$app->request->isGet) {
                    if ($template !== null) {
                        $sendEmailModel->populate($template, $lead->client, $sellerContactInfo);
                    }
                } else {
                    $attr = Yii::$app->request->post();
                    if (isset($attr['extra_body']) && isset($attr['subject'])) {
                        $sendEmailModel->extraBody = $attr['extra_body'];
                        $sendEmailModel->subject = $attr['subject'];
                        $preview = true;
                    }
                    if ($template !== null) {
                        $sendEmailModel->populate($template, $lead->client, $sellerContactInfo);
                    }
                }
                return $this->renderAjax('partial/_sendEmail', [
                    'templates' => $templates,
                    'sendEmailModel' => $sendEmailModel,
                    'lead' => $lead,
                    'preview' => $preview
                ]);
            }
            if (Yii::$app->request->isPost) {
                $attr = Yii::$app->request->post($sendEmailModel->formName());
                $sendEmailModel->attributes = $attr;
                $template = $sendEmailModel->getTemplate();
                if ($template !== null) {
                    $sendEmailModel->populate($template, $lead->client, $sellerContactInfo);
                }
                $isSent = $sendEmailModel->sentEmail($lead);
                if ($isSent) {
                    Yii::$app->getSession()->setFlash('success', sprintf('Sent email \'%s\' succeed.', $sendEmailModel->subject));
                } else {
                    Yii::$app->getSession()->setFlash('danger', sprintf('Sent email \'%s\' failed. Please verify your email or password from email!', $sendEmailModel->subject));
                }
                return $this->redirect([
                    'quote',
                    'type' => 'processing',
                    'id' => $lead->id
                ]);
            } else {
                return $this->renderAjax('partial/_sendEmail', [
                    'templates' => $templates,
                    'sendEmailModel' => $sendEmailModel,
                    'lead' => $lead,
                    'preview' => $preview
                ]);
            }
        }
        throw new BadRequestHttpException();
    }

    public function actionCallExpert($id)
    {
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null && !$lead->called_expert) {
            $data = $lead->getLeadInformationForExpert();
            $data['call_expert'] = true;
            $result = BackOffice::sendRequest('lead/update-lead', 'POST', json_encode($data));

            $lead->notes_for_experts = Yii::$app->request->post('notes');

            if ($result['status'] == 'Success' && empty($result['errors'])) {
                $lead->called_expert = true;
                Yii::$app->getSession()->setFlash('success', 'Call expert request succeeded');
            } else {
                Yii::$app->getSession()->setFlash('warning', print_r($result['errors'], true));
            }
            $lead->save();
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUnprocessed($show)
    {
        if ($show) {
            Yii::$app->response->cookies->remove(Lead::getCookiesKey());
        } else {
            Yii::$app->response->cookies->add(new Cookie([
                'name' => Lead::getCookiesKey(),
                'value' => false,
                'expire' => strtotime('+1 day')
            ]));
        }
        return $this->redirect([
            'queue',
            'type' => 'follow-up'
        ]);
    }

    public function actionAddNote()
    {
        $lead = Lead::findOne(['id' => Yii::$app->request->get('id', 0)]);

        if ($lead !== null && Yii::$app->request->isPost) {
            $model = new Note();
            $attr = Yii::$app->request->post($model->formName());
            $model->attributes = $attr;
            $model->employee_id = Yii::$app->user->identity->getId();
            $model->lead_id = $lead->id;
            $model->save();
        }
        return $this->redirect(Yii::$app->request->referrer);
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
        /**
         * @var $model Lead
         */
        $model = Lead::find()->where([
            'id' => $id
        ])->andWhere([
            'NOT IN', 'status', [Lead::STATUS_BOOKED, Lead::STATUS_SOLD]
        ])->one();
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
                } elseif ($reason->queue == 'reject') {
                    $model->status = $model::STATUS_REJECT;
                    $model->save();
                    return $this->redirect([
                        'queue',
                        'type' => 'trash'
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
            $activeLeads = Lead::find()
                ->where([
                    'status' => [
                        Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING,
                        Lead::STATUS_SNOOZE, Lead::STATUS_FOLLOW_UP
                    ]
                ]);
            $reason = new Reason();
            $reason->queue = $queue;
            return $this->renderAjax('partial/_reason', [
                'reason' => $reason,
                'lead' => $lead,
                'activeLeadIds' => ArrayHelper::map($activeLeads->asArray()->all(), 'id', 'id')
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
                Lead::STATUS_SNOOZE
            ]])->one();

        if ($model === null) {
            if (Yii::$app->request->get('over', 0)) {
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
            } else {
                $model = Lead::findOne([
                    'id' => $id,
                    'employee_id' => Yii::$app->user->identity->getId()
                ]);
                if ($model === null) {
                    Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to access now!');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }

        if (!$model->permissionsView()) {
            throw new UnauthorizedHttpException('Not permissions view lead ID: ' . $id);
        }

        if ($model->status == Lead::STATUS_FOLLOW_UP) {
            $checkProccessingByAgent = LeadFlow::findOne([
                'lead_id' => $model->id,
                'status' => $model::STATUS_PROCESSING,
                'employee_id' => Yii::$app->user->identity->getId()
            ]);
            if ($checkProccessingByAgent === null) {
                $model->called_expert = false;
            }
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
                    $params = Yii::$app->request->queryParams;
                    if (isset($params[$searchModel->formName()])) {
                        $searchModel->employee_id = $params[$searchModel->formName()]['employee_id'];
                    }
                    $dataProvider[$div] = Lead::search($type, $searchModel, $div);
                } else {
                    $dataProvider[$div] = Lead::search($type, null, $div);
                }
            }
        } else if (in_array($type, ['trash'])) {
            $searchModel = new Lead();
            $params = Yii::$app->request->queryParams;
            if (isset($params[$searchModel->formName()])) {
                $searchModel->employee_id = $params[$searchModel->formName()]['employee_id'];
            }
            $dataProvider = Lead::search($type, $searchModel);
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
        $this->view->title = sprintf('Processing Lead - %s', ucfirst($type));

        $lead = Lead::findOne(['id' => $id]);

        if ($lead !== null) {
            Yii::$app->cache->delete(sprintf('quick-search-%d-%d', $lead->id, Yii::$app->user->identity->getId()));
            if (!$lead->permissionsView()) {
                throw new UnauthorizedHttpException('Not permissions view lead ID: ' . $id);
            }
            $leadForm = new LeadForm($lead);
            if ($leadForm->getLead()->status != Lead::STATUS_PROCESSING ||
                $leadForm->getLead()->employee_id != Yii::$app->user->identity->getId()
            ) {
                $leadForm->mode = $leadForm::VIEW_MODE;
            }

            $flightSegments = $leadForm->getLeadFlightSegment();
            foreach ($flightSegments as $segment){
                $this->view->title = sprintf('%s & %s: ',$segment->destination, $id).$this->view->title;
                break;
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

                    if ($lead->called_expert) {
                        $lead = Lead::findOne(['id' => $id]);
                        $data = $lead->getLeadInformationForExpert();
                        $result = BackOffice::sendRequest('lead/update-lead', 'POST', json_encode($data));
                        if ($result['status'] != 'Success' || !empty($result['errors'])) {
                            Yii::$app->getSession()->setFlash('warning', sprintf(
                                'Update info lead for expert failed! %s',
                                print_r($result['errors'], true)
                            ));
                        }
                    }

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
            $leadForm->getLead()->employee_id = \Yii::$app->user->identity->getId();
            $leadForm->getLead()->status = Lead::STATUS_PROCESSING;
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

    public function actionGetUserActions($id)
    {
        $lead = Lead::findOne([
            'id' => $id
        ]);

        $activity = [];
        $quoteId = '';

        if ($lead !== null) {
            if (Yii::$app->request->isPost) {
                $quoteId = Yii::$app->request->post('discountId', $lead->discount_id);
            } else {
                $quoteId = $lead->discount_id;
            }
        }

        if (!empty($quoteId)) {
            $url = $lead->project->link . '/api/user-action-list/' . intval($quoteId);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['apiKey' => $lead->project->api_key]));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            $result = curl_exec($ch);

            $activity = json_decode($result);
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $activity;
        }
        return $this->renderAjax('partial/_requestLog', [
            'activity' => $activity,
            'discountId' => $quoteId,
            'lead' => $lead
        ]);
    }
}
