<?php

namespace webapi\modules\v1\controllers;

use common\models\Employee;
use common\models\Notifications;
use common\models\UserProfile;
use frontend\widgets\notification\NotificationMessage;
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

    //apiDoc was missing and recreated briefly todo double check carefully
    /**
     *
     * @api {post} v1/telegram/index Telegram index action
     * @apiVersion 0.1.0
     * @apiName Index
     * @apiGroup Telegram
     *
     */
    public function actionIndex()
    {
        echo  '<h1>API - Telegram - ' . Yii::$app->request->serverName . '</h1> ' . date('Y-m-d H:i:s');
        exit;
    }

    //apiDoc was missing and recreated briefly todo double check carefully
    /**
     * @api {post} v1/telegram/webhook Send Message To Telegram
     * @apiVersion 0.1.0
     * @apiName Webhook
     * @apiGroup Telegram
     *
     * @apiParam {Array}                                    message                         Message data array
     * @apiParam {string{11}=bot_command}     message.entities.0.type         Type
     * @apiParam {String}                                   message.chat.first_name         Frist Name
     * @apiParam {String}                                   message.chat.username           User Name
     *
     * @apiSuccess {String} message    Message Status
     */
    public function actionWebhook()
    {


        /*$headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }*/

        /*$out = [
            'message'       => 'Server Name: '.Yii::$app->request->serverName,
            'datetime'      => date('Y-m-d H:i:s'),
            'ip'            => Yii::$app->request->getUserIP(),
            'get'           => Yii::$app->request->get(),
            'post'          => Yii::$app->request->post(),
            //'files'         => $_FILES,
            //'headers'       => $headers
        ];*/

        //Yii::info(VarDumper::dumpAsString($out), 'info\API:Telegram:Webhook');

        $result = Yii::$app->request->post();

        /*$result = [
            'update_id' => 831539043,
            'message' => [
                'message_id' => 6,
                'from' => [
                'id' => 270012521,
                    'is_bot' => false,
                    'first_name' => 'Dandy',
                    'username' => 'chalpet',
                    'language_code' => 'ru',
                ],
                'chat' => [
                'id' => 270012521,
                    'first_name' => 'Dandy',
                    'username' => 'chalpet',
                    'type' => 'private',
                ],
                'date' => 1555330114,
                'text' => '/start MTY3fDQ1MzcyMWUzYWY0NGI0ZGViOGZmOGMxMmYyMTkzYzE5',
                'entities' => [
                0 => [
                    'offset' => 0,
                        'length' => 6,
                        'type' => 'bot_command',
                    ]
                ]
            ]
        ];*/

        if ($result) {
            if (isset($result['message']['entities'][0]['type']) && $result['message']['entities'][0]['type'] === 'bot_command') {
                $chat_id =  $result['message']['chat']['id'];

                if (false !== strpos($result['message']['text'], '/start')) {
                    Yii::$app->telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => 'Welcome ' . $result['message']['chat']['first_name'] ?? '' . ' (' . $result['message']['chat']['username'] ?? '' . ')!',
                    ]);


                    Yii::info($result['message']['text'], 'info\API:Telegram:Webhook:command');

                    $codeString = trim(str_replace('/start ', '', $result['message']['text']));

                    $codeString = trim(str_replace('/start', '', $codeString));

                    if ($codeString) {
                        $codeString = @base64_decode($codeString);
                        if ($codeString) {
                            [$user_id, $secure_code] = explode('|', $codeString);
                        } else {
                            $user_id = $secure_code = null;
                        }

                        if ($user_id && $secure_code) {
                            $user = Employee::findOne($user_id);
                            $validCode = md5($user->id . '|' . $user->username . '|' . date('Y-m-d'));


                            if ($secure_code === $validCode) {
                                //Yii::info('Error Telegram Auth. Invalid secure code! ' . $result['message']['text'], 'info\API:Telegram:Webhook:success');

                                $profile = $user->userProfile;
                                if (!$profile) {
                                    $profile = new UserProfile();
                                    $profile->up_user_id = $user->id;
                                }

                                if (!$profile->up_telegram) {
                                    $profile->up_telegram = (string) $chat_id;
                                    $profile->up_telegram_enable = true;
                                    $profile->up_updated_dt = date('Y-m-d H:i:s');
                                    if (!$profile->save()) {
                                        Yii::error(VarDumper::dumpAsString($profile->errors), 'API:Telegram:Webhook:UserProfile:save');

                                        Yii::$app->telegram->sendMessage([
                                            'chat_id' => $chat_id,
                                            'text' => 'Error  Telegram Auth. Not update UserProfile',
                                        ]);

                                        Notifications::create($user->id, 'Telegram Auth', 'Hi, ' . $result['message']['chat']['first_name'] ?? '' . ' (' . $result['message']['chat']['username'] ?? '' . ')! Your Telegram Account is activated. ', Notifications::TYPE_SUCCESS, true);
                                    } else {
                                        if ($ntf = Notifications::create($user->id, 'Telegram Auth', 'Hi, ' . $result['message']['chat']['first_name'] ?? '' . ' (' . $result['message']['chat']['username'] ?? '' . ')! Your Telegram Account is activated. ', Notifications::TYPE_SUCCESS, true)) {
                                            // Notifications::socket($user->id, null, 'getNewNotification', [], true);
                                            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                                            Notifications::publish('getNewNotification', ['user_id' => $user->id], $dataNotification);
                                        }

                                        Yii::$app->telegram->sendMessage([
                                            'chat_id' => $chat_id,
                                            'text' => 'Success Telegram Auth. Hi! ' . $user->username,
                                        ]);
                                    }
                                }
                            } else {
                                Yii::info('Error Telegram Auth. Invalid secure code! ' . $secure_code . ' <> ' . $validCode, 'info\API:Telegram:Webhook:code');

                                Yii::$app->telegram->sendMessage([
                                    'chat_id' => $chat_id,
                                    'text' => 'Error Telegram Auth. Invalid secure code!',
                                ]);
                            }
                        }
                    }
                }
            } else {
                //echo 123;
                Yii::info('Not found bot_command (message/entities[0]/type)', 'info\API:Telegram:Webhook:command');
            }
        }


        Yii::info(VarDumper::dumpAsString($result), 'info\API:Telegram:Webhook:POST');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['message' => 'ok'];
        //VarDumper::dump($result);
    }
}
