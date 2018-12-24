<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\components\CountEvent;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;


/**
 * Test controller
 */
class TestController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'GET'],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }



    public function actionComPreview()
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;
        $data['origin'] = 'ORIGIN';
        $data['destination'] = 'DESTINATION';

        //$mailPreview = $communication->mailPreview(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $data, 'ru-RU');
        //$mailTypes = $communication->mailTypes(7);

        $content_data['email_body_html'] = '1';
        $content_data['email_body_text'] = '2';
        $content_data['email_subject'] = '3';
        $content_data['email_reply_to'] = 'chalpet-r@gmail.com';
        $content_data['email_cc'] = 'chalpet-cc@gmail.com';
        $content_data['email_bcc'] = 'chalpet-bcc@gmail.com';

        $mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);

        /*if($mailSend['data']) {

        }*/






        VarDumper::dump($mailSend, 10, true);

    }

    public function actionSocket()
    {

        $socket = 'tcp://127.0.0.1:1234';
        $user_id = Yii::$app->user->id; //'tester01';
        $lead_id = 12345;
        $data['message'] = 'test '.date('H:i:s');


        try {
            // соединяемся с локальным tcp-сервером
            $instance = stream_socket_client($socket);
            // отправляем сообщение
            if (fwrite($instance, json_encode(['lead_id' => $lead_id, 'multiple' => false, 'data' => $data]) . "\n")) {
                echo 'OK';
            } else {
                echo 'NO';
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        /*$host    = "localhost";
        $port    = 8080;

        $context = stream_context_create();

        $socket = stream_socket_client(
            $host . ':' . $port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        $key = $this->generateWebsocketKey();
        $headers = "HTTP/1.1 101 Switching Protocols\r\n";
        $headers .= "Upgrade: websocket\r\n";
        $headers .= "Connection: Upgrade\r\n";
        $headers .= "Sec-WebSocket-Version: 13\r\n";
        $headers .= "Sec-WebSocket-Key: $key\r\n\r\n";
        stream_socket_sendto($socket, $headers);
        stream_socket_sendto($socket, 'this is my socket test to websocket');*/

        // echo 123;
        ///\yiicod\socketio\Broadcast::emit(CountEvent::name(), ['count' => 10]);
    }


    private function generateWebsocketKey() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$&/()=[]{}0123456789';
        $key = '';
        $chars_length = strlen($chars);
        for ($i = 0; $i < 16; $i++) $key .= $chars[mt_rand(0, $chars_length-1)];
        return base64_encode($key);
    }

}
