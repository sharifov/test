<?php
namespace console\controllers;


use common\components\CommunicationService;
use common\models\Email;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class CommunicationController extends Controller
{


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



        if(isset($res['error']) && $res['error']) {

            print_r($res['error']);


        } elseif(isset($res['data']['emails']) && $res['data']['emails'] && \is_array($res['data']['emails'])) {


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
                $email->e_email_body_html = $mail['ei_email_text'];
                $email->e_created_dt = $mail['ei_created_dt'];

                $email->e_inbox_email_id = $mail['ei_id'];
                $email->e_inbox_created_dt = $mail['ei_created_dt'];
                $email->e_ref_message_id = $mail['ei_ref_mess_ids'];
                $email->e_message_id = $mail['ei_message_id'];

                if(!$email->save()) {
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