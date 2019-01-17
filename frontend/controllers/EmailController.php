<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\EmailTemplateType;
use common\models\ProjectEmployeeAccess;
use common\models\UserProjectParams;
use http\Url;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Yii;
use common\models\Email;
use common\models\search\EmailSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailController implements the CRUD actions for Email model.
 */
class EmailController extends FController
{
    /**
     * {@inheritdoc}
     */


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
                        'actions' => ['inbox', 'view', 'soft-delete'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all Email models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string
     * @throws \yii\httpclient\Exception
     */
    public function actionInbox()
    {
        $searchModel = new EmailSearch();
        $modelNewEmail = new Email();

        if ($modelNewEmail->load(Yii::$app->request->post())) {

            //VarDumper::dump(Yii::$app->request->post('e_send'), 10, true); exit;

            $modelNewEmail->e_status_id = Email::STATUS_NEW;
            $modelNewEmail->e_type_id = Email::TYPE_OUTBOX;

            if($modelNewEmail->e_id && !Yii::$app->request->post('e_send')) {
                $modelNewEmail->e_type_id = Email::TYPE_DRAFT;
            }

            if(!$modelNewEmail->e_project_id) {
                $upp = UserProjectParams::find()->where(['LOWER(upp_email)' => strtolower($modelNewEmail->e_email_from)])->one();
                if($upp && $upp->upp_project_id) {
                    $modelNewEmail->e_project_id = $upp->upp_project_id;
                }
            }

            if(!$modelNewEmail->e_project_id) {
                $modelNewEmail->addError('e_subject', 'Error! Project ID not detected');
            }

            if(!$modelNewEmail->hasErrors() && $modelNewEmail->save()) {

                $error = '';

                if(Yii::$app->request->post('e_send')) {
                    $out = $modelNewEmail->sendMail();

                    if(isset($out['error']) && $out['error']) {
                        $error = $out['error'];
                    }
                }

                //$modelNewEmail->send

                if($error) {
                    $modelNewEmail->addError('c_email_preview', 'Communication Server response: ' . $error);
                    Yii::error($error, 'EmailController:inbox:sendMail');
                } else {
                    return $this->redirect(['email/inbox', 'id' => $modelNewEmail->e_id]);
                }
            }
        } else {

            if(Yii::$app->request->get('email_email')) {
                $modelNewEmail->e_email_from = Yii::$app->request->get('email_email');
            }

            if(Yii::$app->request->get('action') === 'new') {
                $upp = UserProjectParams::find()->where(['LOWER(upp_email)' => strtolower($modelNewEmail->e_email_from)])->one();
                if($upp && $upp->upp_project_id) {
                    $modelNewEmail->e_project_id = $upp->upp_project_id;
                }

                if($modelNewEmail->e_project_id) {



                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;
                    $data['origin'] = '';


                    $content_data['email_body_html'] = '';
                    $content_data['email_subject'] = '';
                    $content_data['content'] = '<br>';

                    //$content_data['email_reply_to'] = ;
                    //$content_data['email_cc'] = 'chalpet-cc@gmail.com';
                    //$content_data['email_bcc'] = 'chalpet-bcc@gmail.com';



                    $projectContactInfo = [];

                    $project = null;

                    if($upp && $project = $upp->uppProject) {
                        $projectContactInfo = @json_decode($project->contact_info, true);
                        //VarDumper::dump($projectContactInfo); exit;
                    }
                    $language = 'en-US';



                    $tpl = 'cl_agent_blank'; // 'cl_agent_blank'//EmailTemplateType::findOne($comForm->c_email_tpl_id);
                    //$mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);


                    //$content_data = []; //$lead->getEmailData2($comForm->quoteList);

                    $content_data['project'] = [
                        'name'      => $project ? $project->name : '',
                        'url'       => $project ? $project->link : 'https://',
                        'address'   => $projectContactInfo['address'] ?? '',
                        'phone'     => $projectContactInfo['phone'] ?? '',
                        'email'     => $projectContactInfo['email'] ?? '',
                    ];

                    $content_data['contacts'] = $content_data['project'];

                    $content_data['agent'] = [
                        'name'  => Yii::$app->user->identity->full_name,
                        'username'  => Yii::$app->user->identity->username,
                        'phone' => $upp && $upp->upp_phone_number ? $upp->upp_phone_number : '',
                        'email' => $upp && $upp->upp_email ? $upp->upp_email : '',
                    ];


                    $mailPreview = $communication->mailPreview($modelNewEmail->e_project_id, $tpl, $modelNewEmail->e_email_from, '', $content_data, $language);

                    if ($mailPreview && isset($mailPreview['data'])) {

                        if (isset($mailPreview['error']) && $mailPreview['error']) {

                            $errorJson = @json_decode($mailPreview['error'], true);
                            $modelNewEmail->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $mailPreview['error']));
                            Yii::error($mailPreview['error'], 'EmailController:inbox:mailPreview');

                            //$modelNewEmail->e_email_body_html = ($errorJson['message'] ?? $mailPreview['error']);

                        } else {
                            $modelNewEmail->e_email_body_html = $mailPreview['data']['email_body_html'];
                        }
                    }

                    $modelNewEmail->e_email_subject = '✈️ ' . ($project ? $project->name : ''). ' - ' . Yii::$app->user->identity->username; //$mailPreview['data']['email_subject'];

                }

            }


        }

        $params = Yii::$app->request->queryParams;
        $params['email_type_id'] = Yii::$app->request->get('email_type_id', Email::FILTER_TYPE_ALL);

        $params['EmailSearch']['e_project_id'] = Yii::$app->request->get('email_project_id');
        $params['EmailSearch']['user_id'] = Yii::$app->user->id;
        $params['EmailSearch']['email'] = Yii::$app->request->get('email_email');


        $dataProvider = $searchModel->searchEmails($params);

        $modelEmailView = null;

        if($e_id = Yii::$app->request->get('id')) {
            $modelEmailView = Email::findOne($e_id);
            if($modelEmailView && $modelEmailView->e_is_new) {
                $modelEmailView->e_is_new = 0;
                $modelEmailView->save();
            }
        }

        if($e_id = Yii::$app->request->get('edit_id')) {

            $modelNewEmail = Email::findOne($e_id);

            /*if($mail) {
                $modelEmailView = new Email();
                $modelEmailView->attributes = 0;
                $modelEmailView->save();
            }*/
        }

        if($e_id = Yii::$app->request->get('reply_id')) {

            $mail = Email::findOne($e_id);

            if($mail) {

                /*if(!$mail->e_project_id) {
                    $upp = UserProjectParams::find()->where(['LOWER(upp_email)' => strtolower($modelNewEmail->e_email_from)])->one();
                    if($upp && $upp->upp_project_id) {
                        $modelNewEmail->e_project_id = $upp->upp_project_id;
                    }
                }*/

                $modelNewEmail = new Email();
                $modelNewEmail->e_project_id = $mail->e_project_id;
                $modelNewEmail->e_email_from = $modelNewEmail->e_type_id == Email::TYPE_INBOX ? $mail->e_email_to : $mail->e_email_from;
                $modelNewEmail->e_email_to = $modelNewEmail->e_type_id == Email::TYPE_INBOX ? $mail->e_email_from : $mail->e_email_to;
                $modelNewEmail->e_email_subject = 'Re: ' . $mail->e_email_subject;

                //$modelNewEmail->e_message_id = $modelNewEmail->generateMessageId();
                // $modelNewEmail->e_email_body_html = '<p>Hi '.Html::encode($modelNewEmail->e_email_to).'!</p><blockquote>'.nl2br(Email::strip_html_tags($mail->e_email_body_html)).'</blockquote><p>The best regards, <br>'.Html::encode(Yii::$app->user->identity->username).'</p>';

                $modelNewEmail->e_email_body_html = '<!DOCTYPE html><html><head><title>Redactor</title><meta charset="UTF-8"/><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /></head><body><p>Hi '.Html::encode($modelNewEmail->e_email_to).'!</p><blockquote>'.nl2br(Email::strip_html_tags($mail->e_email_body_html)).'</blockquote><p>The best regards, <br>'.Html::encode(Yii::$app->user->identity->username).'</p></body></html>';

                $modelNewEmail->e_type_id = Email::TYPE_DRAFT;
                /*if($modelNewEmail->save()) {
                    $this->redirect(\yii\helpers\Url::current(['edit_id' => $modelNewEmail->e_id, 'id' => null, 'reply_id' => null, 'page' => 1]));
                }*/

                //$modelNewEmail = Email::findOne($modelNewEmail->e_id);

            }
        }


        $mailList = [];

        $mails = UserProjectParams::find()->select(['upp_email'])->where(['upp_user_id' => Yii::$app->user->id])->andWhere(['!=', 'upp_email', ''])->asArray()->all();
        if($mails) {
            $mailList = ArrayHelper::map($mails, 'upp_email', 'upp_email');
        }

        $projectList = \common\models\Project::getListByUser(Yii::$app->user->id);


        return $this->render('inbox', [
            //'searchModel'     => $searchModel,
            'dataProvider'      => $dataProvider,
            'modelEmailView'    => $modelEmailView,
            'modelNewEmail'     => $modelNewEmail,
            'mailList'          => $mailList,
            'projectList'       => $projectList,
        ]);
    }

    /**
     * Displays a single Email model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model =$this->findModel($id);

        $is_admin = Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);

        if(!$is_admin) {
            $userAccess = UserProjectParams::find()->where(['or', ['upp_email' => $model->e_email_from], ['upp_email' => $model->e_email_to]])->andWhere(['upp_user_id' => Yii::$app->user->id])->exists();
            if(!$userAccess) {
                throw new AccessDeniedException('Access Denied. Email ID:'.$model->e_id);
            }
        }

        if(Yii::$app->request->get('preview')) {
           return $model->e_email_body_html;
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Email model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Email();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->e_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Email model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->e_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): \yii\web\Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Email model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Email the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Email
    {
        if (($model = Email::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSoftDelete($id): \yii\web\Response
    {
        $model = $this->findModel($id);
        $model->e_is_deleted = (int) ! $model->e_is_deleted;
        $model->save();
        return $this->redirect(Yii::$app->request->referrer);
    }
}
