<?php
namespace webapi\modules\v1\controllers;

use common\models\Employee;
use common\models\Notifications;
use common\models\UserProfile;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Telegram controller
 */
class TelegramController extends Controller
{



    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
        $this->enableCsrfValidation = false;
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo  '<h1>API - Telegram - '.Yii::$app->request->serverName.'</h1> '.date('Y-m-d H:i:s');
        exit;
    }

    /**
     *
     */
    public function actionWebhook(): void
    {

        /*
         *  [
            'ok' => true
            'result' => [
                0 => [
                    'update_id' => 831539038
                    'message' => [
                        'message_id' => 1
                        'from' => [
                            'id' => 270012521
                            'is_bot' => false
                            'first_name' => 'Dandy'
                            'username' => 'chalpet'
                            'language_code' => 'ru'
                        ]
                        'chat' => [
                            'id' => 270012521
                            'first_name' => 'Dandy'
                            'username' => 'chalpet'
                            'type' => 'private'
                        ]
                        'date' => 1555314358
                        'text' => '/start MXwwZWYyMmQ1MzQ3ZDY1NjdhNzc1YmMyNGUyOGFiZTBiMg=='
                        'entities' => [
                            0 => [
                                'offset' => 0
                                'length' => 6
                                'type' => 'bot_command'
                            ]
                        ]
                    ]
                ]
         */


        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'       => 'Server Name: '.Yii::$app->request->serverName,
            'datetime'      => date('Y-m-d H:i:s'),
            'ip'            => Yii::$app->request->getUserIP(),
            'get'           => Yii::$app->request->get(),
            'post'          => Yii::$app->request->post(),
            'files'         => $_FILES,
            'headers'       => $headers
        ];

        Yii::info(VarDumper::dumpAsString($out), 'info\API:Telegram:Webhook');


        $json = @file_get_contents('https://api.telegram.org/bot'.Yii::$app->params['telegram']['token'].'/getUpdates');
        $data = [];
        if($json) {
            $data = @json_decode($json, true);

            if($data && isset($data['result']) && is_array($data['result'])) {
                foreach ($data['result'] as $result) {
                    if(isset($result['message']['entities'][0]['type']) && $result['message']['entities'][0]['type'] === 'bot_command') {
                        if(false !== strpos('/start', $result['message']['text'])) {
                            $codeString = trim(str_replace('/start ', '', $result['message']['text']));
                            if($codeString) {
                                $codeString = @base64_decode($codeString);
                                [$user_id, $secure_code] = explode('|', $codeString);

                                if($user_id && $secure_code) {
                                    $user = Employee::findOne($user_id);
                                    $validCode = md5($user->id . '|' . $user->username . '|' . date('Y-m-d'));
                                    $chat_id =  $result['message']['chat']['id'];

                                    if($secure_code === $validCode) {
                                        $profile = $user->userProfile;
                                        if(!$profile) {
                                            $profile = new UserProfile();
                                            $profile->up_user_id = $user->id;
                                        }



                                        if(!$profile->up_telegram) {
                                            $profile->up_telegram = $chat_id;
                                            $profile->up_telegram_enable = true;
                                            $profile->up_updated_dt = date('Y-m-d Hi:s');
                                            if(!$profile->save()) {
                                                Yii::error(VarDumper::dumpAsString($profile->errors), 'API:Telegram:Webhook:UserProfile:save');
                                            } else {
                                                Notifications::create($user->id, 'Telegram Auth', 'Hi, '.$result['message']['chat']['first_name'].' ('.$result['message']['chat']['username'].')! Your Telegram Account is activated. ', Notifications::TYPE_SUCCESS, true);
                                                Notifications::socket($user->id, null, 'getNewNotification', [], true);

                                                Yii::$app->telegram->sendMessage([
                                                    'chat_id' => $chat_id,
                                                    'text' => 'Success Telegram Auth. Hi! ' . $user->username,
                                                ]);
                                            }
                                        }
                                    } else {
                                        Yii::$app->telegram->sendMessage([
                                            'chat_id' => $chat_id,
                                            'text' => 'Error Telegram Auth. Invalid secure code!',
                                        ]);
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }


        Yii::info(VarDumper::dumpAsString($data), 'info\API:Telegram:Webhook:JSON');

        //VarDumper::dump($data);
    }




}