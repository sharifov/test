<?php

namespace frontend\controllers;

use common\models\search\EmailSearch;
use src\entities\email\Email;
use src\entities\email\EmailSearch as EmailNormalizedSearch;
use src\model\email\useCase\send\EmailSenderService;
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
use src\entities\email\helpers\EmailFilterType;
use common\models\Project;
use src\entities\email\EmailRepository;
use src\entities\email\helpers\EmailContactType;
use common\components\CommunicationService;
use common\models\Employee;
use src\entities\email\EmailBody;

/**
 * EmailNormalizedController implements the CRUD actions for Email model.
 *
 * @property EmailSenderService $emailSender
 * @property EmailRepository $emailRepository
 */
class EmailNormalizedController extends FController
{
    private $emailSender;

    private $emailRepository;

    public function __construct($id, $module, EmailSenderService $emailSender, EmailRepository $emailRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->emailSender = $emailSender;
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

    private function getParamsArray(array $names): array
    {
        $params = [];
        foreach ($names as $name) {
            $params[$name] = Yii::$app->request->get($name);
        }
        return $params;
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
    private function getEmailPreview(Employee $user, $emailFrom, Project $project, UserProjectParams $upp) : array
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;

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
        $user = Auth::user();
        $searchModel = new EmailNormalizedSearch();
        $modelNewEmail = new \common\models\Email();
        $modelEmailView = null;
        $emailForm = null;

        $getParams = $this->getParamsArray([
            'action',
            'email_email',
            'email_type_id',
            'email_project_id',
            'id'
        ]);

        $action = $getParams['action'];

        //TODO: write logic for edit
        if (Yii::$app->request->isPost && in_array($action, ['create', 'reply'])) {
            $emailForm = new EmailCreateForm($user->id);
            if ($emailForm->load(Yii::$app->request->post()) && $emailForm->validate()) {
                try {
                    $email = EmailsNormalizeService::newInstance()->create($emailForm);
                    EmailsNormalizeService::newInstance()->sendMail($email);

                    Yii::$app->session->setFlash('success', 'New Email was created.');

                    return $this->redirect(['inbox', 'id' => $email->e_id]);

                } catch (\Throwable $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage()/* .'<br/>'.$e->getTraceAsString() */);
                }
            } else {
                Yii::$app->session->setFlash('error', $emailForm->getErrorSummary(true));
            }
        } else {
            if ($action == 'create') {
                $formData = [];
                $formData['contacts']['from'] = [
                    'type' => EmailContactType::FROM,
                    'email' => $getParams['email_email']
                ];

                $upp = UserProjectParams::find()->byEmail(strtolower($getParams['email_email']))->one();
                $project = $upp->uppProject ?? null;
                $formData['projectId'] = $upp->upp_project_id ?? null;

                $formData['body']['subject'] = EmailBody::getDraftSubject($project->name ?? '', $user->username);

                try{
                    $mailPreview = $this->getEmailPreview($user, $getParams['email_email'], $project, $upp);
                    $formData['body']['bodyHtml'] = $mailPreview['data']['email_body_html'] ?? null;
                } catch (\Exception $e) {
                    Yii::error($e->getMessage(), 'EmailNormalizedController:inbox:getEmailPreview');
                }

                $emailForm = EmailCreateForm::fromArray($formData);

            } elseif ($action == 'update') {
                $email = $this->findModel($getParams['id']);
                $emailForm = EmailCreateForm::fromModel($email, $user->id);
            } elseif ($action == 'reply') {
                $email = $this->findModel($getParams['id']);
                $emailForm = EmailCreateForm::replyFromModel($email, $user);
            } elseif ($getParams['id']) {
                $modelEmailView = $this->findModel($getParams['id']);
                $this->emailRepository->read($modelEmailView);
            }
        }

        //search provider
        $params = Yii::$app->request->queryParams;
        $params['email_type_id'] = $getParams['email_type_id'] ?? EmailFilterType::ALL;
        $params['EmailSearch']['e_project_id'] = $getParams['email_project_id'];
        $params['EmailSearch']['user_id'] = $user->id;
        $params['EmailSearch']['email'] = $getParams['email_email'];

        $dataProvider = $searchModel->searchEmails($params);
        //==

        //mailList
        $mailList = UserProjectParams::find()
            ->select(['el_email', 'upp_email_list_id'])
            ->where(['upp_user_id' => $user->id])
            ->joinWith('emailList', false, 'INNER JOIN')
            ->indexBy('el_email')
            ->column();

        if ($user && $user->email) {
            $mailList[$user->email] = $user->email;
        }
        //==

        $projectList = Project::getListByUser($user->id);

        //=============

        $stats = [
            'unread' => $this->emailRepository->getUnreadCount($mailList),
            'inboxToday' => $this->emailRepository->getInboxTodayCount($mailList),
            'outboxToday' => $this->emailRepository->getOutboxTodayCount($mailList),
            'draft' => $this->emailRepository->getDraftCount($mailList),
            'trash' => $this->emailRepository->getTrashCount($mailList),
        ];

        return $this->render('inbox', [
            'emailForm'         => $emailForm,
            'mailList'          => $mailList,
            'projectList'       => $projectList,
            'dataProvider'      => $dataProvider,
            'modelEmailView'    => $modelEmailView,
            'selectedId'        => $getParams['id'],
            'action'            => $action,
            'stats'             => $stats,
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
            return $model->emailBody->getBodyHtml() ?: '';
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
        $this->emailRepository->deleteByIds($id);

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
        try{
            return $this->emailRepository->find($id);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
            try {
                $email = EmailsNormalizeService::newInstance()->createEmailFromOld($emailOld);

                $result['html'] = Html::a('<span class="label label-success">yes</span>', ['email-normalized/view', 'id' => $email->e_id], ['target' => '_blank', 'data-pjax' => 0]);

            } catch (\Throwable $e) {
                $result['errors'] =  $e->getMessage() .'<br/>'.$e->getTraceAsString();
            }
        }

        return $this->asJson($result);
    }
}
