<?php

namespace console\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\models\Email;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\services\cases\CasesManageService;
use sales\services\cases\CasesSaleService;
use sales\services\email\EmailService;
use sales\services\email\incoming\EmailIncomingService;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class TestController extends Controller
{
    public function actionEmailIncoming(int $limit = 10)
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;

        $time_start = microtime(true);
        $emailService = Yii::createObject(EmailService::class);

        $emails = Email::find()->limit($limit)->asArray(true)->all();
        $countItems = count($emails);
        $processed = 0;
        Console::startProgress(0, $countItems);

        try {
            foreach ($emails as $mail) {

                $email = new Email();
                $email->e_type_id = Email::TYPE_INBOX;
                $email->e_status_id = Email::STATUS_DONE;
                $email->e_is_new = true;
                $email->e_email_to = $mail['e_email_to'];
                $email->e_email_to_name = $mail['e_email_to_name'] ?? null;
                $email->e_email_from = $mail['e_email_from'];
                $email->e_project_id = $mail['e_project_id'];
                $email->body_html = $mail['e_email_body_text'];
                $email->e_created_dt = $mail['e_created_dt'];
                $email->e_inbox_email_id = $mail['e_id'];
                $email->e_inbox_created_dt =$mail['e_created_dt'];
                $email->e_ref_message_id = $mail['e_ref_message_id'];
                $email->e_message_id = $mail['e_message_id'];
                $email->e_client_id = null;

                if (!$email->save()) {
                    \Yii::error(VarDumper::dumpAsString([
                        'communicationId' => $mail['e_id'],
                        'error' => $email->errors,
                    ]), 'TEST:ReceiveEmailsJob:execute');
                } else {
                    try {
                        $emailIncomingService = Yii::createObject(EmailIncomingService::class);
                        $process = $emailIncomingService->create(
                            $email->e_id,
                            $email->e_email_from,
                            $email->e_email_to,
                            $email->e_project_id
                        );
                        $email->e_lead_id = $process->leadId;
                        $email->e_case_id = $process->caseId;
                        $email->e_client_id = $emailService->detectClientId($email->e_email_from);
                        $email->save(false);
                    } catch (\Throwable $e) {
                        Yii::error($e->getMessage(), 'TEST:ReceiveEmailsJob:EmailIncomingService:create');
                    }

                }

                if ($email->e_case_id) {
                    (Yii::createObject(CasesManageService::class))->needAction($email->e_case_id);

                    try {
                        $job = new CreateSaleFromBOJob();
                        $job->case_id = $email->e_case_id;
                        $job->email = $email->e_email_from;
                        Yii::$app->queue_job->priority(100)->push($job);
                    } catch (\Throwable $throwable) {
                        Yii::error(AppHelper::throwableFormatter($throwable), 'TEST:ReceiveEmailsJob:addToJobFailed');
                    }
                }

                $processed ++;
                Console::updateProgress($processed, $countItems);
            }

        } catch (\Throwable $e) {
            \Yii::error($e, 'TEST:ReceiveEmailsJob:execute');
        }

        Console::endProgress(false);
        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %gFind Items: %w[' . $countItems . '] %g Added to queue: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;
    }

    public function actionCasesUpdateLastAction(int $limit = 10)
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;

        $time_start = microtime(true);


        $cases = Cases::find()->limit($limit)->orderBy(['cs_last_action_dt' => SORT_DESC])->all();
        $countItems = count($cases);
        $processed = 0;
        Console::startProgress(0, $countItems);

        try {
            foreach ($cases as $case) {
                /** @var Cases $case */
                $case->updateLastAction();

                $processed ++;
                Console::updateProgress($processed, $countItems);
            }

        } catch (\Throwable $e) {
            \Yii::error($e, 'TEST:actionCasesUpdateLastAction');
        }

        Console::endProgress(false);
        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %gFind Items: %w[' . $countItems . '] %g Added to queue: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;
    }
}
