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
use src\repositories\email\EmailOldRepository;
use src\entities\email\EmailBody;
use src\entities\email\form\EmailForm;
use src\entities\email\helpers\EmailContactType;
use src\services\email\EmailsNormalizeService;
use src\entities\email\helpers\EmailType;

/**
 * EmailController implements the CRUD actions for Email model.
 *
 * @property EmailMainService $emailService
 * @property EmailRepositoryInterface $emailRepository
 */
class EmailController extends FController
{
    private $emailService;
    private $emailRepository;

    public function __construct($id, $module, EmailMainService $emailService, EmailOldRepository $emailRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->emailService = $emailService;
        $this->emailRepository = $emailRepository;
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
     *
     * @param Employee $user
     * @param string $emailFrom
     * @param Project $project
     * @param UserProjectParams $upp
     * @throws \Exception
     * @return array
     */
    private function getEmailPreview(Employee $user, $emailFrom, Project $project, UserProjectParams $upp): array
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;

        $content_data['email_body_html'] = '';
        $content_data['email_subject'] = '';
        $content_data['content'] = '<br>';

        $projectContactInfo = @json_decode($project->contact_info, true);

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


        $mailPreview = $communication->mailPreview($project->id, 'cl_agent_blank', $emailFrom, '', $content_data);

        if ($mailPreview && isset($mailPreview['data'])) {
            if (isset($mailPreview['error']) && $mailPreview['error']) {
                $errorJson = @json_decode($mailPreview['error'], true);

                throw new \Exception('Communication Server response: ' . ($errorJson['message'] ?? $mailPreview['error']));
            }
        }

        return $mailPreview;
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
        $modelEmailView = null;
        $emailForm = null;
        $action = Yii::$app->request->get('action');
        $selectedId = Yii::$app->request->get('id');
        $emailFrom = Yii::$app->request->get('email_email');

        if (Yii::$app->request->isPost) {
            $emailForm = new EmailForm($user->id);
            if ($emailForm->load(Yii::$app->request->post()) && $emailForm->validate()) {
                try {
                    if (in_array($action, ['create', 'reply'])) {
                        $email = $this->emailService->create($emailForm);
                        $this->emailService->sendMail($email);
                        Yii::$app->session->setFlash('success', 'New Email was created.');
                    } elseif (in_array($action, ['update'])) {
                        $email = $this->findModel($selectedId);
                        $this->emailService->update($email, $emailForm);
                        Yii::$app->session->setFlash('success', 'Email was updated.');
                    }
                    return $this->redirect(['inbox', 'id' => $email->e_id]);
                } catch (\Throwable $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage() . '<br/>' . $e->getTraceAsString());
                }
            } else {
                Yii::$app->session->setFlash('error', $emailForm->getErrorSummary(true));
            }
        } else {
            if ($action == 'create') {
                $formData = [];
                $formData['contacts']['from'] = [
                    'type' => EmailContactType::FROM,
                    'email' => $emailFrom
                ];

                $upp = UserProjectParams::find()->byEmail(strtolower($emailFrom))->one();
                $project = $upp->uppProject ?? null;

                if ($project) {
                    $formData['projectId'] = $upp->upp_project_id ?? null;
                    try {
                        $mailPreview = $this->getEmailPreview($user, $emailFrom, $project, $upp);
                        $formData['body']['bodyHtml'] = $mailPreview['data']['email_body_html'] ?? null;
                    } catch (\Exception $e) {
                        Yii::error($e->getMessage(), 'EmailController:inbox:getEmailPreview');
                    }

                    $formData['body']['subject'] = EmailBody::getDraftSubject($project->name ?? '', $user->username);
                }
                $emailForm = EmailForm::fromArray($formData);
            } elseif ($action == 'update') {
                $email = $this->findModel($selectedId);
                $emailForm = EmailForm::fromArray(EmailsNormalizeService::getDataArrayFromOld($email));
            } elseif ($action == 'reply') {
                $formData = [];
                if ($email = $this->findModel($selectedId)) {
                    $modelNewEmail = new Email();
                    $modelNewEmail->e_project_id = $email->e_project_id;
                    $modelNewEmail->e_email_from = EmailType::isInbox($email->e_type_id) ? $email->e_email_to : $email->e_email_from;
                    $modelNewEmail->e_email_to = EmailType::isInbox($email->e_type_id)? $email->e_email_from : $email->e_email_to;
                    $modelNewEmail->e_email_subject = EmailBody::getReSubject($email->emailSubject);
                    $modelNewEmail->body_html = EmailBody::getReBodyHtml($email->getEmailFrom(false), $user->username, $email->getEmailBodyHtml());
                    $modelNewEmail->e_type_id = EmailType::DRAFT;

                    $formData = EmailsNormalizeService::getDataArrayFromOld($modelNewEmail);
                }
                $emailForm = EmailForm::fromArray($formData);
            } elseif ($selectedId) {
                $modelEmailView = $this->findModel($selectedId);
                $this->emailRepository->read($modelEmailView);
            }
        }

        $params = Yii::$app->request->queryParams;
        $params['email_type_id'] = Yii::$app->request->get('email_type_id', Email::FILTER_TYPE_ALL);
        $params['EmailSearch']['e_project_id'] = Yii::$app->request->get('email_project_id');
        $params['EmailSearch']['user_id'] = $user->id;
        $params['EmailSearch']['email'] = Yii::$app->request->get('email_email');

        $dataProvider = $searchModel->searchEmails($params);

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

        $stats = [
            'unread' => $this->emailRepository->getUnreadCount($mailList),
            'inboxToday' => $this->emailRepository->getInboxTodayCount($mailList),
            'outboxToday' => $this->emailRepository->getOutboxTodayCount($mailList),
            'draft' => $this->emailRepository->getDraftCount($mailList),
            'trash' => $this->emailRepository->getTrashCount($mailList),
        ];

        return $this->render('inbox', [
            //'searchModel'     => $searchModel,
            'dataProvider'      => $dataProvider,
            'modelEmailView'    => $modelEmailView,
            'mailList'          => $mailList,
            'projectList'       => $projectList,
            'selectedId'        => $selectedId,
            'action'            => $action,
            'stats'             => $stats,
            'emailForm'         => $emailForm,
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
