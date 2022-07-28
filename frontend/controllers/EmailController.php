<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\Email;
use common\models\Employee;
use common\models\search\EmailSearch;
use common\models\UserProjectParams;
use frontend\widgets\newWebPhone\email\form\EmailSendForm;
use http\Url;
use modules\email\src\abac\dto\EmailAbacDto;
use modules\email\src\abac\EmailAbacObject;
use src\auth\Auth;
use src\model\emailList\entity\EmailList;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use src\services\email\EmailMainService;
use src\dto\email\EmailDTO;
use src\exception\CreateModelException;
use yii\helpers\VarDumper;
use src\exception\EmailNotSentException;
use src\helpers\text\StringHelper;
use common\models\Project;

/**
 * EmailController implements the CRUD actions for Email model.
 *
 * @property EmailMainService $emailService
 */
class EmailController extends FController
{
    private $emailService;

    public function __construct($id, $module, EmailMainService $emailService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->emailService = $emailService;
    }

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
                'allowActions' => [
                    'view',
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

        $params = Yii::$app->request->queryParams;
        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['EmailSearch']['supervision_id'] = Yii::$app->user->id;
        }
        $searchModel->date_range = null;

        $dataProvider = $searchModel->search($params);

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
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $searchModel = new EmailSearch();
        $modelNewEmail = new Email();

        if ($modelNewEmail->load(Yii::$app->request->post())) {
            //VarDumper::dump(Yii::$app->request->post('e_send'), 10, true); exit;

            $modelNewEmail->e_status_id = Email::STATUS_NEW;
            $modelNewEmail->e_type_id = Email::TYPE_OUTBOX;

            if ($modelNewEmail->e_id && !Yii::$app->request->post('e_send')) {
                $modelNewEmail->e_type_id = Email::TYPE_DRAFT;
            }

            if (!$modelNewEmail->e_project_id) {
//                $upp = UserProjectParams::find()->where(['LOWER(upp_email)' => strtolower($modelNewEmail->e_email_from)])->one();
                $upp = UserProjectParams::find()->byEmail(strtolower($modelNewEmail->e_email_from))->one();
                if ($upp && $upp->upp_project_id) {
                    $modelNewEmail->e_project_id = $upp->upp_project_id;
                }
            }



            if (!$modelNewEmail->e_project_id) {
                $modelNewEmail->addError('e_subject', 'Error! Project ID not detected');
            }

            if (!$modelNewEmail->hasErrors() && $modelNewEmail->save()) {
                $error = '';

                if (Yii::$app->request->post('e_send')) {
                    $out = $modelNewEmail->sendMail();

                    if (isset($out['error']) && $out['error']) {
                        $error = $out['error'];
                    }
                }

                //$modelNewEmail->send

                if ($error) {
                    $modelNewEmail->addError('c_email_preview', 'Communication Server response: ' . $error);
                    Yii::error($error, 'EmailController:inbox:sendMail');
                } else {
                    return $this->redirect(['email/inbox', 'id' => $modelNewEmail->e_id]);
                }
            }
        } else {
            if (Yii::$app->request->get('email_email')) {
                $modelNewEmail->e_email_from = Yii::$app->request->get('email_email');
            }

            if (Yii::$app->request->get('action') === 'new') {
//                $upp = UserProjectParams::find()->where(['LOWER(upp_email)' => strtolower($modelNewEmail->e_email_from)])->one();
                $upp = UserProjectParams::find()->byEmail(strtolower($modelNewEmail->e_email_from))->one();
                if ($upp && $upp->upp_project_id) {
                    $modelNewEmail->e_project_id = $upp->upp_project_id;
                }

                if ($modelNewEmail->e_project_id) {



                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->comms;
                    $data['origin'] = '';


                    $content_data['email_body_html'] = '';
                    $content_data['email_subject'] = '';
                    $content_data['content'] = '<br>';

                    //$content_data['email_reply_to'] = ;
                    //$content_data['email_cc'] = 'chalpet-cc@gmail.com';
                    //$content_data['email_bcc'] = 'chalpet-bcc@gmail.com';



                    $projectContactInfo = [];

                    $project = null;

                    if ($upp && $project = $upp->uppProject) {
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
//                        'phone' => $upp && $upp->upp_tw_phone_number ? $upp->upp_tw_phone_number : '',
                        'phone' => $upp && $upp->getPhone() ? $upp->getPhone() : '',
//                        'email' => $upp && $upp->upp_email ? $upp->upp_email : '',
                        'email' => $upp && $upp->getEmail() ? $upp->getEmail() : '',
                    ];


                    $mailPreview = $communication->mailPreview($modelNewEmail->e_project_id, $tpl, $modelNewEmail->e_email_from, '', $content_data, $language);

                    if ($mailPreview && isset($mailPreview['data'])) {
                        if (isset($mailPreview['error']) && $mailPreview['error']) {
                            $errorJson = @json_decode($mailPreview['error'], true);
                            $modelNewEmail->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $mailPreview['error']));
                            Yii::error($mailPreview['error'], 'EmailController:inbox:mailPreview');
                        } else {
                            $modelNewEmail->body_html = $mailPreview['data']['email_body_html'];
                        }
                    }

                    $modelNewEmail->e_email_subject = '✈️ ' . ($project ? $project->name : '') . ' - ' . Yii::$app->user->identity->username; //$mailPreview['data']['email_subject'];
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

        if ($e_id = Yii::$app->request->get('id')) {
            $modelEmailView = Email::findOne($e_id);

            if ($modelEmailView && $modelEmailView->e_is_new) {
                $modelEmailView->e_is_new = 0;
                $modelEmailView->save();
            }
        }

        if ($e_id = Yii::$app->request->get('edit_id')) {
            $modelNewEmail = Email::findOne($e_id);
            $modelNewEmail->body_html = $modelNewEmail->emailBodyHtml;
            /*if($mail) {
                $modelEmailView = new Email();
                $modelEmailView->attributes = 0;
                $modelEmailView->save();
            }*/
        }

        if ($e_id = Yii::$app->request->get('reply_id')) {
            $mail = Email::findOne($e_id);

            if ($mail) {
                /*if(!$mail->e_project_id) {
                    $upp = UserProjectParams::find()->where(['LOWER(upp_email)' => strtolower($modelNewEmail->e_email_from)])->one();
                    if($upp && $upp->upp_project_id) {
                        $modelNewEmail->e_project_id = $upp->upp_project_id;
                    }
                }*/

                $modelNewEmail = new Email();
                $modelNewEmail->e_project_id = $mail->e_project_id;
                $modelNewEmail->e_email_from = $mail->e_type_id == Email::TYPE_INBOX ? $mail->e_email_to : $mail->e_email_from;
                $modelNewEmail->e_email_to = $mail->e_type_id == Email::TYPE_INBOX ? $mail->e_email_from : $mail->e_email_to;
                $modelNewEmail->e_email_subject = Email::reSubject($mail->e_email_subject);

                //$modelNewEmail->e_message_id = $modelNewEmail->generateMessageId();

                $modelNewEmail->body_html = '<!DOCTYPE html><html><head><title>Redactor</title><meta charset="UTF-8"/><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /></head><body><p>Hi ' . Html::encode($modelNewEmail->e_email_to) . '!</p><blockquote>' . nl2br(StringHelper::stripHtmlTags($mail->getEmailBodyHtml())) . '</blockquote><p>The best regards, <br>' . Html::encode(Yii::$app->user->identity->username) . '</p></body></html>';

                $modelNewEmail->e_type_id = Email::TYPE_DRAFT;
                /*if($modelNewEmail->save()) {
                    $this->redirect(\yii\helpers\Url::current(['edit_id' => $modelNewEmail->e_id, 'id' => null, 'reply_id' => null, 'page' => 1]));
                }*/

                //$modelNewEmail = Email::findOne($modelNewEmail->e_id);
            }
        }


//        $mailList = [];

//        $mails = UserProjectParams::find()->select(['upp_email'])->where(['upp_user_id' => Yii::$app->user->id])->andWhere(['!=', 'upp_email', ''])->asArray()->all();
//        $mails = UserProjectParams::find()
//            ->select([EmailList::tableName() . '.el_email', 'upp_email_list_id'])
//            ->where(['upp_user_id' => Yii::$app->user->id])
//            ->joinWith('emailList', true, 'INNER JOIN')
//            ->asArray()->all();
//        if ($mails) {
////            $mailList = ArrayHelper::map($mails, 'upp_email', 'upp_email');
//            $mailList = ArrayHelper::map($mails, 'el_email', 'el_email');
//        }

        $mailList = UserProjectParams::find()
            ->select(['el_email', 'upp_email_list_id'])
            ->where(['upp_user_id' => Yii::$app->user->id])
            ->joinWith('emailList', false, 'INNER JOIN')
            ->indexBy('el_email')
            ->column();

        if ($user && $user->email) {
            $mailList[$user->email] = $user->email;
        }

        $projectList = Project::getListByUser($user->id);

        return $this->render('inbox', [
            //'searchModel'     => $searchModel,
            'dataProvider'      => $dataProvider,
            'modelEmailView'    => $modelEmailView,
            'modelNewEmail'     => $modelNewEmail,
            'mailList'          => $mailList,
            'projectList'       => $projectList,
            'selectedId'        => Yii::$app->request->get('id'),
        ]);
    }


    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $model = $this->findModel($id);

        /*if (!Auth::can('email/view', ['email' => $model])) {
            throw new ForbiddenHttpException('Access denied.');
        }*/

        /** @abac new EmailAbacDto($model), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS, Restrict access to view emails on case or lead*/

        if (!Yii::$app->abac->can(new EmailAbacDto($model), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if (Yii::$app->request->get('preview')) {
            return $model->getEmailBodyHtml() ?: '';
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

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->e_id]);
            }
        } else {
            $model->body_html = $model->emailBodyHtml;
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

    public function actionSend(): Response
    {
        $user = Auth::user();

        $form = new EmailSendForm($user);

        $result = [
            'success' => false,
            'errors' => [],
        ];

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                try{
                    $emailDTO = EmailDTO::fromArray([
                        'projectId' => $form->getProjectId(),
                        'emailSubject' => $form->subject,
                        'bodyHtml' => $form->text,
                        'emailFrom' => $form->userEmail,
                        'emailFromName' => $form->user->full_name,
                        'emailToName' => $form->getContactName(),
                        'emailTo' => $form->getContactEmail(),
                        'createdUserId' => $form->user->id,
                    ]);
                    $mail = $this->emailService->createFromDTO($emailDTO, false);
                    $this->emailService->sendMail($mail);
                    $result['success'] = true;
                } catch (CreateModelException $e) {
                    $result['errors'] = $e->getErrors();
                    Yii::error(VarDumper::dumpAsString($e->getErrors()), 'EmailController:send:Email:save');
                } catch (EmailNotSentException $e) {
                    $error = $e->getMessage();
                    $result['errors'] = ['communication' => [$error]];
                    Yii::error('Error: Email Message has not been sent to ' . $mail->getEmailTo(false) . "\r\n" . $error, 'EmailController:send:Email:sendMail');
                }
            } else {
                $result['errors'] = $form->getErrors();
            }
        } else {
            $result['errors'] = ['data' => ['Not found post data']];
        }

        return $this->asJson($result);
    }
}
