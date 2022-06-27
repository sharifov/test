<?php

namespace frontend\controllers;

use common\models\search\EmailSearch;
use src\entities\email\Email;
use src\entities\email\EmailSearch as EmailNormalizedSearch;
use src\model\email\useCase\send\EmailSenderService;
use src\dispatchers\EventDispatcher;
use src\services\email\EmailsNormalizeService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\email\src\abac\dto\EmailAbacDto;
use modules\email\src\abac\EmailAbacObject;
use yii\web\ForbiddenHttpException;
use yii\bootstrap\Html;
use src\auth\Auth;
use common\models\UserProjectParams;
use src\entities\email\form\EmailCreateForm;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailFilterType;
use common\models\Project;

/**
 * EmailNormalizedController implements the CRUD actions for Email model.
 *
 * @property EmailSenderService $emailSender
 * @property EventDispatcher $eventDispatcher
 */
class EmailNormalizedController extends FController
{
    private $emailSender;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct($id, $module, EmailSenderService $emailSender, EventDispatcher $eventDispatcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->emailSender = $emailSender;
        $this->eventDispatcher = $eventDispatcher;
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
        $searchModel = new EmailNormalizedSearch();

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
        $user = Auth::user();
        $searchModel = new EmailNormalizedSearch();
        $modelNewEmail = new Email();

        if ($modelNewEmail->load(Yii::$app->request->post())) {
            $modelNewEmail->e_status_id = EmailStatus::NEW;
            $modelNewEmail->e_type_id = EmailType::OUTBOX;

            if ($modelNewEmail->e_id && !Yii::$app->request->post('e_send')) {
                $modelNewEmail->e_type_id = EmailType::DRAFT;
            }

            if (!$modelNewEmail->e_project_id) {
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
                $upp = UserProjectParams::find()->byEmail(strtolower($modelNewEmail->e_email_from))->one();
                if ($upp && $upp->upp_project_id) {
                    $modelNewEmail->e_project_id = $upp->upp_project_id;
                }

                if ($modelNewEmail->e_project_id) {
                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;
                    $data['origin'] = '';

                    $content_data['email_body_html'] = '';
                    $content_data['email_subject'] = '';
                    $content_data['content'] = '<br>';

                    $projectContactInfo = [];

                    $project = null;

                    if ($upp && $project = $upp->uppProject) {
                        $projectContactInfo = @json_decode($project->contact_info, true);
                    }
                    $language = 'en-US';

                    $tpl = 'cl_agent_blank';

                    $content_data['project'] = [
                        'name'      => $project ? $project->name : '',
                        'url'       => $project ? $project->link : 'https://',
                        'address'   => $projectContactInfo['address'] ?? '',
                        'phone'     => $projectContactInfo['phone'] ?? '',
                        'email'     => $projectContactInfo['email'] ?? '',
                    ];

                    $content_data['contacts'] = $content_data['project'];

                    $content_data['agent'] = [
                        'name'  => $user->full_name,
                        'username'  => $user->username,
                        'phone' => $upp && $upp->getPhone() ? $upp->getPhone() : '',
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

                    $modelNewEmail->e_email_subject = '✈️ ' . ($project ? $project->name : '') . ' - ' . $user->username; //$mailPreview['data']['email_subject'];
                }
            }
        }

        $params = Yii::$app->request->queryParams;
        $params['email_type_id'] = Yii::$app->request->get('email_type_id', EmailFilterType::ALL);

        $params['EmailSearch']['e_project_id'] = Yii::$app->request->get('email_project_id');
        $params['EmailSearch']['user_id'] = Yii::$app->user->id;
        $params['EmailSearch']['email'] = Yii::$app->request->get('email_email');


        $dataProvider = $searchModel->searchEmails($params);

        $modelEmailView = null;

        if ($e_id = Yii::$app->request->get('id')) {
            $modelEmailView = Email::findOne($e_id);

            if ($modelEmailView && $modelEmailView->isNew()) {
                $modelEmailView->emailLog->el_is_new = 0;
                $modelEmailView->emailLog->save();
            }
        }

        if ($e_id = Yii::$app->request->get('edit_id')) {
            $modelNewEmail = Email::findOne($e_id);
            $modelNewEmail->body_html = $modelNewEmail->emailBodyHtml;
        }

        if ($e_id = Yii::$app->request->get('reply_id')) {
            $mail = Email::findOne($e_id);

            if ($mail) {
                $modelNewEmail = new Email();
                $modelNewEmail->e_project_id = $mail->e_project_id;
                $modelNewEmail->e_email_from = $mail->e_type_id == Email::TYPE_INBOX ? $mail->e_email_to : $mail->e_email_from;
                $modelNewEmail->e_email_to = $mail->e_type_id == Email::TYPE_INBOX ? $mail->e_email_from : $mail->e_email_to;
                $modelNewEmail->e_email_subject = Email::reSubject($mail->e_email_subject);

                $modelNewEmail->body_html = '<!DOCTYPE html><html><head><title>Redactor</title><meta charset="UTF-8"/><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /></head><body><p>Hi ' . Html::encode($modelNewEmail->e_email_to) . '!</p><blockquote>' . nl2br(Email::stripHtmlTags($mail->getEmailBodyHtml())) . '</blockquote><p>The best regards, <br>' . Html::encode($user->username) . '</p></body></html>';

                $modelNewEmail->e_type_id = Email::TYPE_DRAFT;
            }
        }

        $mailList = UserProjectParams::find()
            ->select(['el_email', 'upp_email_list_id'])
            ->where(['upp_user_id' => $user->id])
            ->joinWith('emailList', false, 'INNER JOIN')
            ->indexBy('el_email')
            ->column();

        if ($user && $user->email) {
            $mailList[$user->email] = $user->email;
        }

        $projectList = Project::getListByUser($user->id);

        //=============
        $form = new EmailCreateForm($user->id);
        $action = null;

        if (Yii::$app->request->get('action') === 'new') {
            $action = 'create';
        } elseif (Yii::$app->request->get('edit_id')) {
            $action = 'update';
        } elseif (Yii::$app->request->get('reply_id')) {
            $action = 'reply';
        }

        return $this->render('inbox', [
            'createform'        => $form,
            'mailList'          => $mailList,
            'projectList'       => $projectList,
            'dataProvider'      => $dataProvider,
            'modelEmailView'    => $modelEmailView,
            'modelNewEmail'     => $modelNewEmail,
            'selectedId'        => Yii::$app->request->get('id'),
            'action'            => $action
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
        /** @abac new EmailAbacDto($model), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS, Restrict access to view emails on case or lead*/

/*         if (!Yii::$app->abac->can(new EmailAbacDto($model), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied.');
        }
 */
        if (Yii::$app->request->get('preview')) {
            return $model->getEmailBodyHtml() ?: '';
        }

        return $this->render('view', [
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
        $email = $this->findModel($id);
        $email->delete();
        $this->eventDispatcher->dispatchAll($email->releaseEvents());

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

    public function actionNormalize($id)
    {
        $result = [
            'success' => false,
            'errors' => [],
        ];

        if (($emailOld = \common\models\Email::findOne($id)) !== null) {
            $service = new EmailsNormalizeService();
            $service->createEmailFromOld($emailOld);

            if ($service->email !== null) {
                $result['success'] =  true;
                $result['html'] = Html::a('<span class="label label-success">yes</span>', ['email-normalized/view', 'id' => $service->email->e_id], ['target' => '_blank', 'data-pjax' => 0]);
            }
            else {
                $result['errors'] = $service->getErrors();
            }
        }


        return $this->asJson($result);
    }
}
