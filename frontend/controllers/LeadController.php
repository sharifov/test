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
use common\models\LeadTask;
use common\models\local\LeadAdditionalInformation;
use common\models\Note;
use common\models\ProjectEmailTemplate;
use common\models\Reason;
use common\models\Task;
use common\models\UserProjectParams;
use frontend\models\LeadForm;
use frontend\models\SendEmailForm;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\widgets\ActiveForm;
use common\models\LeadFlightSegment;
use common\models\Quote;
use common\models\Employee;
use common\models\search\LeadSearch;
use frontend\models\ProfitSplitForm;

/**
 * Site controller
 */
class LeadController extends FController
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
                            'check-updates', 'flow-transition', 'get-user-actions', 'add-pnr', 'update2','clone',
                            'get-badges', 'sold', 'split-profit', 'processing', 'follow-up', 'inbox', 'trash', 'booked'
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
                //Yii::$app->setLayoutPath('@frontend/views/layouts');
                //$this->layout = 'sale';
                $this->layout = '@app/themes/gentelella/views/layouts/main_lead';
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

    public function actionGetBadges()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = Lead::getBadgesSingleQuery();
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

            $userProjectParams = UserProjectParams::findOne([
                'upp_user_id' => $sendEmailModel->employee->id,
                'upp_project_id' => $sendEmailModel->project->id
            ]);


            if(!$userProjectParams) {
                throw new BadRequestHttpException('Not found UserProjectParams (user_id: '.$sendEmailModel->employee->id.', project_id: '.$sendEmailModel->project->id.' )');
            }

            $templates = ProjectEmailTemplate::getTypesForSellers();
            if (Yii::$app->request->isAjax) {
                $sendEmailModel->type = Yii::$app->request->get('type');
                $template = $sendEmailModel->getTemplate();
                if (Yii::$app->request->isGet) {
                    if ($template !== null) {
                        $sendEmailModel->populate($template, $lead->client, $userProjectParams);
                    }
                } else {
                    $attr = Yii::$app->request->post();
                    if (isset($attr['extra_body']) && isset($attr['subject'])) {
                        $sendEmailModel->extraBody = $attr['extra_body'];
                        $sendEmailModel->subject = $attr['subject'];
                        $preview = true;
                    }
                    if ($template !== null) {
                        $sendEmailModel->populate($template, $lead->client, $userProjectParams);
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
                    $sendEmailModel->populate($template, $lead->client, $userProjectParams);
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
            'follow-up',
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

        $type = 'inbox';

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
                        'trash',
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
                    $model->save();
                    return $this->redirect([
                        'follow-up',
                    ]);
                } elseif ($reason->queue == 'trash') {
                    $model->status = $model::STATUS_TRASH;
                    $type = 'trash';
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
                        'trash',
                    ]);
                } else {
                    $model->status = $model::STATUS_ON_HOLD;
                }

                $model->save();
            }
        }

        return $this->redirect([
            'processing',
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
                ])->andWhere(['<>', 'id', $id]);

            $activeLeadIds = ArrayHelper::map($activeLeads->asArray()->all(), 'id', 'id');
            $activeLeadIds = $activeLeadIds ?: [];

            $reason = new Reason();
            $reason->queue = $queue;
            return $this->renderAjax('partial/_reason', [
                'reason' => $reason,
                'lead' => $lead,
                'activeLeadIds' => $activeLeadIds
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

        $user = Yii::$app->user->identity;



        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        /*if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }*/


        $inProcessing = Lead::find()
            ->where([
                'employee_id' => $user->getId(),
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
                    'employee_id' => $user->getId()
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


        if($model->status == Lead::STATUS_PENDING && $isAgent) {
            $isAccessNewLead = $user->accessTakeNewLead();
            if(!$isAccessNewLead) {
                throw new NotAcceptableHttpException('Access is denied (limit) - "Take lead"');
            }
        }

        if ($model->status == Lead::STATUS_FOLLOW_UP) {
            $checkProccessingByAgent = LeadFlow::findOne([
                'lead_id' => $model->id,
                'status' => $model::STATUS_PROCESSING,
                'employee_id' => $user->getId()
            ]);
            if ($checkProccessingByAgent === null) {
                $model->called_expert = false;
            }
        }


        $model->employee_id = $user->getId();

        if ($model->status != Lead::STATUS_ON_HOLD && $model->status != Lead::STATUS_SNOOZE && !$model->l_answered) {
            LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_NOT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_NOT_ANSWERED_PROCESS);
        }

        if($model->l_answered && $model->status == Lead::STATUS_SNOOZE) {
            LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_ANSWERED_PROCESS);
        }



        $model->status = Lead::STATUS_PROCESSING;
        $model->save();


        //$taskList = ['call1', 'call2', 'voice-mail', 'email'];



        return $this->redirect([
            'quote',
            'type' => 'processing',
            'id' => $model->id
        ]);

    }

    public function actionQueue($type)
    {
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
            'searchModel' => $searchModel,
            'type' => $type
        ]);
    }

    public function actionSold()
    {
        $searchModel = new LeadSearch();
        $salary = null;
        $salaryBy = '';

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        if($isAgent) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
            $employee = Employee::findOne(['id' => Yii::$app->user->id]);

            if((!isset($params['LeadSearch']['sold_date_from']) && !isset($params['LeadSearch']['sold_date_to'])) ||
                (empty($params['LeadSearch']['sold_date_from']) && empty($params['LeadSearch']['sold_date_to']))){
                $start = new \DateTime();
                $end = new \DateTime();
                $start->modify('first day of this month');
                $end->modify('last day of this month');
                $salaryBy = $start->format('M Y');
            }else{
                if(!empty($params['LeadSearch']['sold_date_from'])){
                    $start = \DateTime::createFromFormat('d-M-Y', $params['LeadSearch']['sold_date_from']);
                }else{
                    $start = null;
                }
                if(!empty($params['LeadSearch']['sold_date_to'])){
                    $end = \DateTime::createFromFormat('d-M-Y', $params['LeadSearch']['sold_date_to']);
                }else{
                    $end = null;
                }

                $today = new \DateTime();
                if($start !== null && $end !== null){
                    $salaryBy = "(".$start->format('j M').' - '.$end->format('j M Y').')';
                }elseif($start !== null){
                    $salaryBy =  "(".$start->format('j M').' - '.$today->format('j M Y').')';
                }elseif($end !== null){
                    $salaryBy =  '(till '.$end->format('j M Y').')';
                }
            }

            $salary = $employee->calculateSalaryBetween($start, $end);
        }

        $dataProvider = $searchModel->searchSold($params);

        return $this->render('sold', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
            'salary' => $salary,
            'salaryBy' => $salaryBy,
        ]);
    }


    public function actionProcessing()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchProcessing($params);

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    public function actionFollowUp()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchFollowUp($params);

        return $this->render('follow-up', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    public function actionInbox()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }


        $checkShiftTime = true;

        if($isAgent) {
            $user = Yii::$app->user->identity;
            /** @var Employee $user */
            $checkShiftTime = $user->checkShiftTime();
            $userParams = $user->userParams;

            if($userParams) {
                if($userParams->up_inbox_show_limit_leads > 0) {
                    $params['LeadSearch']['limit'] = $userParams->up_inbox_show_limit_leads;
                }
            }


            /*if($checkShiftTime = !$user->checkShiftTime()) {
                throw new ForbiddenHttpException('Access denied! Invalid Agent shift time');
            }*/
        }

        $checkShiftTime = true;



        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchInbox($params);

        $user = Yii::$app->user->identity;

        $isAccessNewLead = $user->accessTakeNewLead();

        return $this->render('inbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'checkShiftTime' => $checkShiftTime,
            'isAgent' => $isAgent,
            'isAccessNewLead' => $isAccessNewLead,
            'user' => $user
        ]);
    }


    public function actionTrash()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchTrash($params);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    public function actionBooked()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchBooked($params);

        return $this->render('booked', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
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


    public function actionUpdate2()
    {

        //echo 123; exit;

        $lead_id = (int) Yii::$app->request->get('id');
        $action = Yii::$app->request->get('act');
        $lead = Lead::findOne(['id' => $lead_id]);
        if(!$lead) {
            throw new NotFoundHttpException('Not found lead ID: ' . $lead_id);
        }

        if($action === 'answer') {
            $lead->l_answered = $lead->l_answered ? 0 : 1;
            if($lead->update()) {
                if($lead->l_answered) {
                    LeadTask::deleteAll('lt_lead_id = :lead_id AND lt_date >= :date AND lt_completed_dt IS NULL',
                        [':lead_id' => $lead->id, ':date' => date('Y-m-d') ]);

                    LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', Task::CAT_ANSWERED_PROCESS);
                    LeadTask::createTaskList($lead->id, $lead->employee_id, 2, '', Task::CAT_ANSWERED_PROCESS);
                    LeadTask::createTaskList($lead->id, $lead->employee_id, 3, '', Task::CAT_ANSWERED_PROCESS);

                } else {
                    LeadTask::deleteAll('lt_lead_id = :lead_id AND lt_date >= :date AND lt_completed_dt IS NULL',
                        [':lead_id' => $lead->id, ':date' => date('Y-m-d') ]);

                    LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
                }
            }
        }

        $referrer = Yii::$app->request->referrer; //$_SERVER["HTTP_REFERER"];
        return $this->redirect($referrer);
    }

    public function actionQuote($type, $id)
    {
        $this->view->title = sprintf('Processing Lead - %s', ucfirst($type));

        $lead = Lead::findOne(['id' => $id]);

        if ($lead !== null) {


            if (Yii::$app->request->post('hasEditable')) {

                $value = '456';
                $message = '';

                // use Yii's response format to encode output as JSON
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                // read your posted model attributes
                if (Yii::$app->request->isPost && $taskNotes = Yii::$app->request->post('task_notes')) {

                    $taskId = $taskDate = $userId = $leadId = null;

                    $leadId = $lead->id; //Yii::$app->request->get('lead_id');

                    $taskKey = key($taskNotes);

                    if($taskKey) {
                        list($taskId, $taskDate, $userId) = explode('_', $taskKey);
                    }

                    $value = $taskNotes[$taskKey];


                    if(!$taskId) {
                        $message = 'Not found Task ID data';
                    } elseif(!$taskDate) {
                        $message = 'Not found Task Date data';
                    } elseif(!$userId) {
                        $message = 'Not found Task User ID data';
                    } elseif(!$leadId) {
                        $message = 'Not found Lead ID data';
                    } else {

                        if($taskDate && $taskId && $leadId && $userId) {
                            $lt = LeadTask::find()->where(['lt_lead_id' => $leadId, 'lt_date' => $taskDate, 'lt_task_id' => $taskId, 'lt_user_id' => $userId])->one();
                            if($lt) {
                                $lt->lt_notes = $value;
                                $lt->lt_updated_dt = date('Y-m-d H:i:s');
                                $lt->update();
                            }
                        }

                    }

                } else {
                    $message = 'Not found task notes data';
                }


                return ['output' => nl2br(Html::encode($value)), 'message' => $message];
            }


            if(Yii::$app->request->isPjax) {
                $taskDate = Yii::$app->request->get('date');
                $taskId = Yii::$app->request->get('task_id');
                $leadId = $lead->id; //Yii::$app->request->get('lead_id');
                $userId = Yii::$app->request->get('user_id'); // Yii::$app->user->id;

                if($taskDate && $taskId && $leadId && $userId) {
                    $lt = LeadTask::find()->where(['lt_lead_id' => $leadId, 'lt_date' => $taskDate, 'lt_task_id' => $taskId, 'lt_user_id' => $userId])->one();
                    if($lt) {
                        if($lt->lt_completed_dt) {
                            $lt->lt_completed_dt = null;
                        } else {
                            $lt->lt_completed_dt = date('Y-m-d H:i:s');
                        }
                        $lt->lt_updated_dt = date('Y-m-d H:i:s');
                        $lt->update();
                    }
                }

            }




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


            if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
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
                $model = $leadForm->getLead();
                LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
                LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_NOT_ANSWERED_PROCESS);
                LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_NOT_ANSWERED_PROCESS);

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

            $result = null;
            if($lead->project) {
                $projectLink = $lead->project->link;
                $projectLink = str_replace('www.', '', $projectLink);

                $url = $projectLink . '/api/user-action-list/' . intval($quoteId);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['apiKey' => $lead->project->api_key]));
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                $result = curl_exec($ch);
            }

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

    public function actionClone($id)
    {
        $errors = [];
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $newLead = new Lead();
            $newLead->attributes = $lead->attributes;
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('partial/_clone', [
                    'lead' => $newLead,
                    'errors' => $errors,
                ]);
            }elseif (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post();

                if($data['Lead']['description'] != 0){
                    if(isset(Lead::CLONE_REASONS[$data['Lead']['description']])){
                        $newLead->description = Lead::CLONE_REASONS[$data['Lead']['description']];
                    }
                }else{
                    if(isset($data['other'])){
                        $newLead->description = trim($data['other']);
                    }
                }
                $newLead->status = Lead::STATUS_PROCESSING;
                $newLead->clone_id = $id;
                $newLead->employee_id = Yii::$app->user->id;
                $newLead->notes_for_experts = null;
                $newLead->rating = 0;
                $newLead->additional_information = null;
                $newLead->l_answered = 0;
                $newLead->l_grade = 0;
                $newLead->snooze_for = null;
                $newLead->called_expert = false;
                $newLead->created = null;
                $newLead->updated = null;

                if(!$newLead->save()){
                    $errors = array_merge($errors, $newLead->getErrors());
                }

                if(empty($errors)){
                    $flightSegments = LeadFlightSegment::findAll(['lead_id' => $id]);
                    foreach ($flightSegments as $segment){
                        $flightSegment = new LeadFlightSegment();
                        $flightSegment->attributes = $segment->attributes;
                        $flightSegment->lead_id = $newLead->id;
                        if (!$flightSegment->save()) {
                            $errors = array_merge($errors, $flightSegment->getErrors());
                        }
                    }
                }

                if(!empty($errors)){
                    return $this->renderAjax('partial/_clone', [
                        'lead' => $newLead,
                        'errors' => $errors,
                    ]);
                }else{
                    Lead::sendClonedEmail($newLead);
                    return $this->redirect([
                        'quote',
                        'type' => 'processing',
                        'id' => $newLead->id
                    ]);
                }
            }

        }
        return null;
    }

    public function actionSplitProfit($id)
    {
        $errors = [];
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $totalProfit = $lead->getBookedQuote()->getTotalProfit();
            $splitForm = new ProfitSplitForm($lead);

            $mainAgentProfit = $totalProfit;

           if (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post();

                if(!isset($data['ProfitSplit'])){
                    $data['ProfitSplit'] = [];
                }

                $load = $splitForm->loadModels($data);
                if ($load) {
                    $errors = ActiveForm::validate($splitForm);
                }

                if (empty($errors) && $splitForm->save($errors)) {
                    return $this->redirect([
                        'quote',
                        'type' => 'sold',
                        'id' => $lead->id
                    ]);
                }

                $splitProfit = $splitForm->getProfitSplit();
                if(!empty($splitProfit)){
                    $percentSum = 0;
                    foreach ($splitProfit as $entry){
                        if(!empty($entry->ps_percent)){
                            $percentSum += $entry->ps_percent;
                        }
                    }
                    $mainAgentProfit -= $totalProfit*$percentSum/100;
                }

                if(!empty($errors)){
                    return $this->renderAjax('_split_profit', [
                        'lead' => $lead,
                        'splitForm' => $splitForm,
                        'totalProfit' => $totalProfit,
                        'mainAgentProfit' => $mainAgentProfit,
                        'errors' => $errors,
                    ]);
                }
            }elseif (Yii::$app->request->isAjax){
                return $this->renderAjax('_split_profit', [
                    'lead' => $lead,
                    'splitForm' => $splitForm,
                    'totalProfit' => $totalProfit,
                    'mainAgentProfit' => $mainAgentProfit,
                    'errors' => $errors,
                ]);
            }

        }
        return null;
    }
}
