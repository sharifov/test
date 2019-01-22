<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\UserProjectParams;
use frontend\models\SmsInboxForm;
use phpDocumentor\Reflection\Types\This;
use Yii;
use common\models\Sms;
use common\models\search\SmsSearch;
use frontend\controllers\FController;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\CommunicationService;

/**
 * SmsController implements the CRUD actions for Sms model.
 */
class SmsController extends FController
{
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'inbox', 'soft-delete'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    /**
     * Lists all Sms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $inboxModel = new SmsInboxForm();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'inboxModel' => $inboxModel,
        ]);
    }

    /**
     * Lists all Sms models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new SmsSearch();

        $params = Yii::$app->request->queryParams;
        $params['SmsSearch']['user_id'] = Yii::$app->user->id;
        //$params['SmsSearch']['phone'] = Yii::$app->request->get('sms_phone');
        $params['SmsSearch']['s_is_deleted'] = 0;

        $dataProvider = $searchModel->searchSms($params);
        $phoneList = Employee::getPhoneList(Yii::$app->user->id);

        //VarDumper::dump($phoneList, 10, true);

        $projectList = \common\models\Project::getListByUser(Yii::$app->user->id);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'phoneList'          => $phoneList,
            'projectList'       => $projectList,
        ]);
    }

    /**
     * Displays a single Sms model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView2($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess($model);

        if($model->s_is_new) {
            $model->s_read_dt = date('Y-m-d H:i:s');
            $model->s_is_new = false;
            $model->save();
        }

        return $this->render('view2', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Sms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sms();

        if ($model->load(Yii::$app->request->post())) {

            $upp = UserProjectParams::find()->where(['upp_phone_number' => $model->s_phone_from])->orWhere(['upp_tw_phone_number' => $model->s_phone_from])->limit(1)->one();
            if($upp && $upp->upp_project_id) {
                $model->s_project_id = $upp->upp_project_id;
            } else {
                $model->s_project_id = null;
            }

            $model->s_created_user_id = Yii::$app->user->id;
            $model->s_created_dt = date('Y-m-d H:i:s');
            $model->s_status_id = Sms::STATUS_PROCESS;
            $model->s_type_id = Sms::TYPE_OUTBOX;

            if($model->save()) {
                $smsResponse = $model->sendSms();

                if(isset($smsResponse['error']) && $smsResponse['error']) {
                    Yii::$app->session->setFlash('danger', 'Error: <strong>SMS Message</strong> has not been sent to <strong>'.$model->s_phone_to.'</strong>');
                    Yii::error('Error: SMS Message has not been sent to '.$model->s_phone_to."\r\n ".$smsResponse['error'], 'SmsController:create:Sms:sendSms');
                    $model->addError('s_sms_text', 'Error: SMS Message has not been sent to '.$model->s_phone_to.".\r\n ".$smsResponse['error']);
                } else {
                    Yii::$app->session->setFlash('success', '<strong>SMS Message</strong> has been successfully sent to <strong>'.$model->s_phone_to.'</strong>');
                    return $this->redirect(['view2', 'id' => $model->s_id]);
                }

            }
        }


        $phoneList = Employee::getPhoneList(Yii::$app->user->id);

        return $this->render('create', [
            'model' => $model,
            'phoneList' => $phoneList,
        ]);
    }

    /**
     * Updates an existing Sms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $upp = UserProjectParams::find()->where(['upp_phone_number' => $model->s_phone_from])->orWhere(['upp_tw_phone_number' => $model->s_phone_from])->limit(1)->one();
            if($upp && $upp->upp_project_id) {
                $model->s_project_id = $upp->upp_project_id;
            } else {
                $model->s_project_id = null;
            }

            $model->s_updated_user_id = Yii::$app->user->id;
            $model->s_updated_dt = date('Y-m-d H:i:s');

            if($model->save()) {
                return $this->redirect(['view', 'id' => $model->s_id]);
            }
        }

        $phoneList = Employee::getPhoneList(Yii::$app->user->id);

        return $this->render('update', [
            'model' => $model,
            'phoneList' => $phoneList,
        ]);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionSoftDelete($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess($model);
        $model->s_is_deleted = true;
        $model->save();

        return $this->redirect(['list']);
    }


    /**
     * @return \yii\web\Response
     */
    public function actionAllDelete()
    {
        $phoneList = Employee::getPhoneList(Yii::$app->user->id);

        //VarDumper::dump($phoneList, 10, true); exit;

        Sms::updateAll(['s_is_deleted' => true], ['s_is_deleted' => false, 's_phone_from' => $phoneList]);
        Sms::updateAll(['s_is_deleted' => true], ['s_is_deleted' => false, 's_phone_to' => $phoneList]);
        return $this->redirect(['list']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionAllRead()
    {
        $phoneList = Employee::getPhoneList(Yii::$app->user->id);
        Sms::updateAll(['s_is_new' => false, 's_read_dt' => date('Y-m-d H:i:s')], ['s_read_dt' => null, 's_phone_from' => $phoneList]);
        Sms::updateAll(['s_is_new' => false, 's_read_dt' => date('Y-m-d H:i:s')], ['s_read_dt' => null, 's_phone_to' => $phoneList]);
        return $this->redirect(['list']);
    }


    /**
     * Finds the Sms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sms::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param Sms $model
     * @throws ForbiddenHttpException
     */
    protected function checkAccess(Sms $model) : void
    {
        $phoneList = [];

        $phoneList[$model->s_phone_to] = $model->s_phone_to;
        $phoneList[$model->s_phone_from] = $model->s_phone_from;

        $access = UserProjectParams::find()->where(['upp_user_id' => Yii::$app->user->id])
            ->andWhere(['or', ['upp_tw_phone_number' => $phoneList], ['upp_phone_number' => $phoneList]])->exists();

        if(!$access) {
            //throw new ForbiddenHttpException('Access denied for this SMS. Check User Project Params phones');
        }
    }


    public function actionInbox()
    {
        $errors = 0;

        /** @var CommunicationService $com */
        $com = Yii::$app->communication; //get('communication');
        $model = new SmsInboxForm();
        $total = 0;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $filter = [];

            $last_n = $model->last_n;
            $last_date = $model->last_date;
            $action = $model->action;

            if ($action == 'all') {
                $filter = [];
            } elseif ($action == 'last_n') {
                if ($last_n > 0) {
                    $filter['limit'] = $last_n;
                }
                if ($last_date != '') {
                    $filter['last_dt'] = $last_date;
                }
            } elseif ($action == 'last_date') {
                if ($last_date != '') {
                    $filter['last_dt'] = $last_date;
                }
            } elseif ($action == 'last_id') {
                $sms_last_id_q = Sms::find()->where('s_type_id = ' . Sms::TYPE_INBOX)->orderBy(['s_communication_id' => SORT_DESC])->one();
                if ($sms_last_id_q) {
                    $sms_last_id = $sms_last_id_q->s_communication_id;
                    $filter['last_id'] = $sms_last_id;
                }
            } else {
                $filter['limit'] = 100;
            }
            //VarDumper::dump($filter, 10, true); exit;
            $data = $com->smsGetMessages($filter);

            if (isset($data['data'], $data['data']['sms']) && count($data['data']['sms'])) {
                foreach ($data['data']['sms'] AS $key => $smsItem) {
                    $find = Sms::findOne(['s_tw_message_sid' => $smsItem['si_message_sid']]);
                    if (NULL === $find) {
                        $total++;
                        $sms = new Sms();
                        $sms->s_type_id = Sms::TYPE_INBOX;
                        $sms->s_status_id = Sms::STATUS_DONE;
                        $sms->s_is_new = true;
                        $sms->s_status_done_dt = isset($smsItem['si_sent_dt']) ? date('Y-m-d H:i:s', strtotime($smsItem['si_sent_dt'])) : null;
                        $sms->s_communication_id = $smsItem['si_id'] ?? null;

                        $sms->s_phone_to = $smsItem['si_phone_to'];
                        $sms->s_phone_from = $smsItem['si_phone_from'];
                        $sms->s_project_id = $smsItem['si_project_id'] ?? null;
                        $sms->s_sms_text = $smsItem['si_sms_text'];
                        $sms->s_created_dt = $smsItem['si_created_dt'];

                        $sms->s_tw_message_sid = $smsItem['si_message_sid'] ?? null;
                        $sms->s_tw_num_segments = $smsItem['si_num_segments'] ?? null;

                        $sms->s_tw_to_country = $smsItem['si_to_country'] ?? null;
                        $sms->s_tw_to_state = $smsItem['si_to_state'] ?? null;
                        $sms->s_tw_to_city = $smsItem['si_to_city'] ?? null;
                        $sms->s_tw_to_zip = $smsItem['si_to_zip'] ?? null;

                        $sms->s_tw_from_country = $smsItem['si_from_country'] ?? null;
                        $sms->s_tw_from_city = $smsItem['si_from_city'] ?? null;
                        $sms->s_tw_from_state = $smsItem['si_from_state'] ?? null;
                        $sms->s_tw_from_zip = $smsItem['si_from_zip'] ?? null;

                        $sms->detectLeadId();

                        if (!$sms->save()) {
                            Yii::error(VarDumper::dumpAsString($sms->errors), 'Frontend:SmsController:inbox:save');
                            $errors++;
                        }
                    }
                }
            }
        }
        if ($errors > 0) {
            Yii::$app->session->setFlash('error', $errors . '  Errors');
        } else {
            Yii::$app->session->setFlash('success', $total . ' Sms items received');
        }

        return $this->redirect(['index']);
    }
}
