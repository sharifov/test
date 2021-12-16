<?php

namespace frontend\controllers;

use common\components\purifier\Purifier;
use common\models\Email;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\auth\Auth;
use sales\entities\cases\CaseEventLog;
use sales\forms\emailReviewQueue\EmailReviewQueueForm;
use sales\model\emailReviewQueue\entity\EmailReviewQueue;
use sales\model\emailReviewQueue\entity\EmailReviewQueueSearch;
use sales\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class EmailReviewQueueController extends FController
{
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

        $email = $model->erqEmail;
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
                if ($email->save()) {
                    $mailResponse = $email->sendMail();

                    if (empty($mailResponse['error'])) {
                        $emailQueue->statusToReviewed();
                        $emailQueue->erq_user_reviewer_id = Auth::id();
                        $emailQueue->save();

                        if ($case = $email->eCase) {
                            $case->addEventLog(CaseEventLog::EMAIL_REVIEWED, ($email->eTemplateType->etp_name ?? '') . ' email sent. By: ' . $email->e_email_from_name, [], CaseEventLog::CATEGORY_INFO);
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
                $email = $emailReviewQueue->erqEmail;
                $message = 'Email(' . $emailReviewQueue->erq_email_id . ') was rejected by (' . $emailReviewQueue->erqUserReviewer->username . ')';
                $email->statusToCancel();
                $email->e_error_message = $message;
                $email->update();
                if ($email->e_lead_id && ($lead = $email->eLead)) {
                    $message .= '<br> Lead (Id: ' . Purifier::createLeadShortLink($lead) . ')';
                }
                if ($email->e_case_id && ($case = $email->eCase)) {
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
            'displayActionBtns' => true
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
