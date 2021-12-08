<?php

namespace frontend\controllers;

use common\models\Email;
use sales\auth\Auth;
use sales\entities\cases\CaseEventLog;
use sales\forms\emailReviewQueue\EmailReviewQueueForm;
use sales\model\emailReviewQueue\entity\EmailReviewQueue;
use sales\model\emailReviewQueue\entity\EmailReviewQueueSearch;
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
                    'index',
                    'review',
                    'send',
                    'reject'
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

    public function actionReview($id): string
    {
        $model = $this->findModel($id);

        $email = $model->erqEmail;
        $previewForm = new EmailReviewQueueForm($email, $model->erq_id);
        $model->erq_user_reviewer_id = Auth::id();
        $model->save();
        return $this->render('review', [
            'model' => $model,
            'email' => $email,
            'previewForm' => $previewForm
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
                        $emailQueue->save();

                        if ($case = $email->eCase) {
                            $case->addEventLog(CaseEventLog::EMAIL_REVIEWED, ($email->eTemplateType->etp_name ?? '') . ' email sent. By: ' . $email->e_email_from_name, [], CaseEventLog::CATEGORY_INFO);
                        }
                        \Yii::$app->session->setFlash('success', 'Email was sent to ' . $email->e_email_to);
                        return $this->redirect('/email-review-queue/index');
                    }
                    $form->addError('general', 'Error: Email Message has not been sent to ' .  $email->e_email_to . '; Reason: ' . $email->e_error_message);
                }
            }
        }
        return $this->render('partial/_preview_email', [
            'previewForm' => $form
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
            if ($emailReviewQueue->save()) {
                \Yii::$app->session->setFlash('warning', 'Email was rejected');
                return $this->redirect('/email-review-queue/index');
            }
        }
        return $this->render('partial/_preview_email', [
            'previewForm' => $form
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
