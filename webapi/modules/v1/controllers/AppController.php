<?php
namespace webapi\modules\v1\controllers;

use Yii;
use yii\helpers\VarDumper;

class AppController extends ApiBaseController
{

    /**
     * @api {get, post} /v1/app/test API Test action
     * @apiVersion 0.1.0
     * @apiName TestApp
     * @apiGroup App
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "message": "Server Name: api.host.test",
     *      "code": 0,
     *      "date": "2018-05-30",
     *      "time": "16:01:17",
     *      "ip": "127.0.0.1",
     *      "get": [],
     *      "post": [],
     *      "files": [],
     *      "headers": {
     *          "Accept-Language": "ru,en-US;q=0.9,en;q=0.8,zh;q=0.7,zh-TW;q=0.6,zh-CN;q=0.5,ko;q=0.4,de;q=0.3",
     *          "Accept-Encoding": "gzip, deflate",
     *          "Dnt": "1",
     *          "Accept": "*\/*",
     *          "Postman-Token": "6ce239ad-5e05-cc88-13d1-ba2ff5538720",
     *          "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViYQ==",
     *          "Cache-Control": "no-cache",
     *          "User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36",
     *          "Connection": "keep-alive",
     *          "Host": "api.bookair.zeit.test"
     *      }
     *  }
     *
     *
     * @return array
     */
    public function actionTest()
    {

        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (strpos($name, 'HTTP_') === 0)
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: '.Yii::$app->request->serverName,
            'code'      => 0,
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        Yii::info(VarDumper::dumpAsString($out), 'info\API:AppController:Test');

        return $out;
    }

}
