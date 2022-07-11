<?php

namespace frontend\controllers;

use common\components\purifier\Purifier;
use common\models\Email;
use common\models\Notifications;
use common\models\Quote;
use common\models\QuoteCommunication;
use frontend\helpers\JsonHelper;
use frontend\models\CommunicationForm;
use frontend\widgets\notification\NotificationMessage;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\services\url\UrlGenerator;
use src\auth\Auth;
use src\entities\cases\CaseEventLog;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use src\model\emailReviewQueue\entity\EmailReviewQueueSearch;
use src\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use src\repositories\quote\QuoteRepository;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class EmailReviewQueueController extends FController
{
    private UrlGenerator $fileStorageUrlGenerator;
    private QuoteRepository $quoteRepository;

    public function __construct($id, $module, UrlGenerator $fileStorageUrlGenerator, QuoteRepository $quoteRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->fileStorageUrlGenerator = $fileStorageUrlGenerator;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'review',
                    'send',
                    'reject',
                    'pending',
                    'completed'
                ],
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionIndex(): string
    {
        $search = new EmailReviewQueueSearch();
        $dataProvider = $search->reviewQueue($this->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $search,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionPending(): string
    {
        $search = new EmailReviewQueueSearch();
        $search->setPendingScenario();
        $dataProvider = $search->reviewQueueByStatuses($this->request->queryParams, Auth::user(), array_keys(EmailReviewQueueStatus::getPendingList()));

        return $this->render('pending', [
            'searchModel' => $search,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCompleted(): string
    {
        $search = new EmailReviewQueueSearch();
        $search->setCompletedScenario();
        $dataProvider = $search->reviewQueueByStatuses($this->request->queryParams, Auth::user(), array_keys(EmailReviewQueueStatus::getCompletedList()));

        return $this->render('completed', [
            'searchModel' => $search,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionReview($id): string
    {
        $model = $this->findModel($id);

        $isReview = (bool)\Yii::$app->request->get('review', false);
        $isTake = (bool)\Yii::$app->request->get('take', false);

        $email = $model->email;
        $previewForm = new EmailReviewQueueForm($email, $model->erq_id);
        $displayActionBtns = false;
        if ($isReview && $model->isPending()) {
            $model->erq_user_reviewer_id = Auth::id();
            $model->statusToInProgress();
            $model->save();
            $displayActionBtns = true;
        } elseif ($isTake && $model->canTake(Auth::id())) {
            $model->erq_user_reviewer_id = Auth::id();
            $model->save();
            $displayActionBtns = true;
        }

        if ($model->canReview(Auth::id())) {
            $displayActionBtns = true;
        }
        return $this->render('review', [
            'model' => $model,
            'email' => $email,
            'previewForm' => $previewForm,
            'displayActionBtns' => $displayActionBtns
        ]);
    }

    public function actionSend()
    {
        if (!\Yii::$app->request->isPost && !\Yii::$app->request->isPjax) {
            throw new BadRequestHttpException('Method not allowed');
        }

        $form = new EmailReviewQueueForm(null, null);
        $form->sendEmailScenario();
        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $email = Email::findOne($form->emailId);
            $emailQueue = $this->findModel($form->emailQueueId);
            if ($email) {
                $email->e_email_from = $form->emailFrom;
                $email->e_email_from_name = $form->emailFromName;
                $email->e_email_to = $form->emailTo;
                $email->e_email_to_name = $form->emailToName;
                $email->e_email_subject = $form->emailSubject;
                $email->e_status_id = Email::STATUS_PENDING;
                $email->body_html = $form->emailMessage;
                $attachments = JsonHelper::decode($email->e_email_data);

                if ($email->save()) {
                    $mailResponse = $email->sendMail($attachments);

                    if (empty($mailResponse['error'])) {
                        $emailQueue->statusToReviewed();
                        $emailQueue->erq_user_reviewer_id = Auth::id();
                        $emailQueue->save();

                        if ($case = $email->eCase) {
                            $case->addEventLog(CaseEventLog::EMAIL_REVIEWED, ($email->eTemplateType->etp_name ?? '') . ' email sent. By: ' . $email->e_email_from_name, [], CaseEventLog::CATEGORY_INFO);
                        }

                        if ($email->e_lead_id) {
                            /*
                             * TODO: The similar logic exist in `\frontend\controllers\LeadController::actionView`. Need to shrink code duplications.
                             */
                            $quoteIdSubquery = (new Query())
                                ->select(['qc_quote_id'])
                                ->from(['qc' => QuoteCommunication::tableName()])
                                ->where('qc_communication_type=:communication_type', [':communication_type' => CommunicationForm::TYPE_EMAIL])
                                ->distinct();
                            /** @var Quote[] $quotes */
                            $quotes = Quote::find()->where(['IN', 'id', $quoteIdSubquery])->all();
                            foreach ($quotes as $quote) {
                                $quote->setStatusSend();
                                $this->quoteRepository->save($quote);
                            }
                        }

                        \Yii::$app->session->setFlash('success', 'Email(' . $email->e_id . ') was sent to ' . $email->e_email_to);
                        return $this->redirect('/email-review-queue/index');
                    }
                    $form->addError('general', 'Error: Email Message has not been sent to ' .  $email->e_email_to . '; Reason: ' . $email->e_error_message);
                }
            }
        }
        return $this->render('partial/_preview_email', [
            'previewForm' => $form,
            'displayActionBtns' => true
        ]);
    }

    public function actionReject()
    {
        if (!\Yii::$app->request->isPost && !\Yii::$app->request->isPjax) {
            throw new BadRequestHttpException('Method not allowed');
        }

        $form = new EmailReviewQueueForm(null, null);
        $form->rejectEmailScenario();
        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $emailReviewQueue = $this->findModel($form->emailQueueId);
            $emailReviewQueue->statusToReject();
            $emailReviewQueue->erq_user_reviewer_id = Auth::id();
            if ($emailReviewQueue->save()) {
                $email = $emailReviewQueue->email;
                $message = 'Email(' . $emailReviewQueue->erq_email_id . ') was rejected by (' . $emailReviewQueue->erqUserReviewer->username . ')';
                $email->statusToCancel($message);
                if ($lead = $emailReviewQueue->emailLead) {
                    $message .= '<br> Lead (Id: ' . Purifier::createLeadShortLink($lead) . ')';
                }
                if ($case = $emailReviewQueue->emailCase) {
                    $message .= '<br> Case (Id: ' . Purifier::createCaseShortLink($case) . ')';
                }
                if ($ntf = Notifications::create($emailReviewQueue->erq_owner_id, 'Email(' . $emailReviewQueue->erq_email_id . ') was rejected by (' . $emailReviewQueue->erqUserReviewer->username . ')', $message, Notifications::TYPE_WARNING, true)) {
                    $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $emailReviewQueue->erq_owner_id], $dataNotification);
                }
                \Yii::$app->session->setFlash('warning', 'Email(' . $emailReviewQueue->erq_email_id . ') was rejected');
                return $this->redirect('/email-review-queue/index');
            }
        }
        return $this->render('partial/_preview_email', [
            'previewForm' => $form,
            'displayActionBtns' => true,
            'files' => []
        ]);
    }

    protected function findModel($id): EmailReviewQueue
    {
        if (($model = EmailReviewQueue::findOne(['erq_id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
