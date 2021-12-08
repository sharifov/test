<?php

namespace console\controllers;

use common\components\CommunicationService;
use common\components\ReceiveEmailsJob;
use common\models\DepartmentEmailProject;
use common\models\Email;
use common\models\UserProjectParams;
use sales\model\emailList\entity\EmailList;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseConsole;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\queue\Queue;

class CommunicationController extends Controller
{
    public function actionExecuteJobGetEmails()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $lastId = BaseConsole::input('Enter Last Communication Id: ');
        $limit = BaseConsole::input('Enter Limit: ');

        $lastId = (int)$lastId;
        $limit = (int)$limit;

        $job = new ReceiveEmailsJob();

        $job->request_data = [
            'last_email_id' => $lastId,
            'email_list' => $this->getEmailsForReceivedMessages(),
            'limit' => $limit,
        ];

        $job->last_email_id = $lastId;

        $job->execute('job');

        printf("\n --- Finish %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionRunJobGetEmails()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $lastId = BaseConsole::input('Enter Last Communication Id: ');
        $limit = BaseConsole::input('Enter Limit: ');

        $lastId = (int)$lastId;
        $limit = (int)$limit;

        $job = new ReceiveEmailsJob();

        $job->request_data = [
            'last_email_id' => $lastId,
            'email_list' => $this->getEmailsForReceivedMessages(),
            'limit' => $limit,
        ];

        $job->last_email_id = $lastId;
        /** @var Queue $queue */
        $queue = \Yii::$app->queue_email_job;
        $jobId = $queue->push($job);
        $response = [
            'job_id' => $jobId,
            'last_id' => $lastId,
        ];

        VarDumper::dump($response);

        printf("\n --- Finish %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @return array
     */
    private function getEmailsForReceivedMessages(): array
    {
//        $mailsUpp = UserProjectParams::find()->select(['DISTINCT(upp_email)'])->andWhere(['!=', 'upp_email', ''])->column();
        $mailsUpp = UserProjectParams::find()->select('el_email')->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
//        $mailsDep = DepartmentEmailProject::find()->select(['DISTINCT(dep_email)'])->andWhere(['!=', 'dep_email', ''])->column();
        $mailsDep = DepartmentEmailProject::find()->select(['el_email'])->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
        $list = array_merge($mailsUpp, $mailsDep);
        return $list;
    }

    public function actionGetMails()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;

        $filter = [];
        $dateTime = null;

        //$filter['last_dt'] = '';

        /*$email_to = Yii::$app->request->post('email_to');
    $email_from = Yii::$app->request->post('email_from');
    $limit = Yii::$app->request->post('limit');
    $offset = Yii::$app->request->post('offset');
    $new = Yii::$app->request->post('new');
    $last_id = Yii::$app->request->post('last_id');
    $last_dt = Yii::$app->request->post('last_dt');*/

        $res = $communication->mailGetMessages($filter);

        //print_r($res); exit;



        if (isset($res['error']) && $res['error']) {
            print_r($res['error']);
        } elseif (isset($res['data']['emails']) && $res['data']['emails'] && \is_array($res['data']['emails'])) {
            /*
            * @property int $ei_id
            * @property string $ei_email_to
            * @property string $ei_email_from
            * @property string $ei_email_subject
            * @property string $ei_email_text
            * @property string $ei_email_category
            * @property int $ei_project_id
            * @property bool $ei_new
            * @property bool $ei_deleted
            * @property string $ei_created_dt
            * @property string $ei_updated_dt
            * @property string $ei_ref_mess_ids
            * @property string $ei_message_id
                */


            foreach ($res['data']['emails'] as $mail) {
                print_r($mail['ei_id']);


                $email = new Email();

                $email->e_type_id = Email::TYPE_INBOX;
                $email->e_status_id = Email::STATUS_DONE;
                $email->e_is_new = true;

                $email->e_email_to = $mail['ei_email_to'];
                $email->e_email_from = $mail['ei_email_from'];
                $email->e_email_subject = $mail['ei_email_subject'];
                $email->e_project_id = $mail['ei_project_id'];
                $email->body_html = $mail['ei_email_text'];
                $email->e_created_dt = $mail['ei_created_dt'];

                $email->e_inbox_email_id = $mail['ei_id'];
                $email->e_inbox_created_dt = $mail['ei_created_dt'];
                $email->e_ref_message_id = $mail['ei_ref_mess_ids'];
                $email->e_message_id = $mail['ei_message_id'];

                \Yii::info(
                    $email->toArray(),
                    'info\Debug:CommunicationController:email'
                );
                /* TODO: FOR DEBUG:: must by remove */

                if (!$email->save()) {
                    Yii::error(VarDumper::dumpAsString($email->errors), 'API:Communication:newMessagesReceived:Email:save');
                }
            }

            /*if($eq_status_id > 0) {
                $email->e_status_id = $eq_status_id;
                if($eq_status_id === Email::STATUS_DONE) {
                    $email->e_status_done_dt = date('Y-m-d H:i:s');
                }


            }*/
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}
