<?php

namespace webapi\modules\v2\controllers;

use common\models\UserGroup;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\SuccessResponse;

/**
 * Class UserGroupController
 */
class UserGroupController extends BaseController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['HttpCache'] = [
            'class' => 'yii\filters\HttpCache',
            'only' => ['list'],
            'lastModified' => static function ($action, $params) {
                return strtotime(UserGroup::find()->max('ug_updated_dt'));
            },
        ];
        return $behaviors;
    }

    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['list'] = ['GET'];
        return $verbs;
    }

    /**
     * @api {get} /v2/user-group/list Get User Groups
     * @apiVersion 0.2.0
     * @apiName UserGroupList
     * @apiGroup UserGroup
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeader {string} If-Modified-Since  Format <code> day-name, day month year hour:minute:second GMT</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     * @apiHeaderExample {json} Header-Example (If-Modified-Since):
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate",
     *      "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT"
     *  }
     *
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *   {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *           "user-group": [
     *               {
     *                   "ug_id": 1,
     *                   "ug_key": "ug1",
     *                   "ug_name": "Bucuresti Team",
     *                  "ug_disable": 0,
     *                   "ug_updated_dt": "2018-12-18 09:17:45"
     *               },
     *               {
     *                   "ug_id": 2,
     *                   "ug_key": "ug2",
     *                   "ug_name": "100J Team",
     *                  "ug_disable": 0,
     *                   "ug_updated_dt": "2018-12-18 09:17:59"
     *               },
     *               {
     *                   "ug_id": 3,
     *                   "ug_key": "ug3",
     *                   "ug_name": "Pro Team",
     *                   "ug_disable": 1,
     *                   "ug_updated_dt": "2018-12-18 09:18:10"
     *               },
     *           ]
     *       },
     *       "technical": {
     *           "action": "v2/user-group/list",
     *           "response_id": 8080269,
     *           "request_dt": "2020-02-27 15:00:43",
     *           "response_dt": "2020-02-27 15:00:43",
     *           "execution_time": 0.006,
     *           "memory_usage": 189944
     *       },
     *       "request": []
     *   }
     *
     * @apiSuccessExample {json} Not Modified-Response (304):
     *
     * HTTP/1.1 304 Not Modified
     * Cache-Control: public, max-age=3600
     * Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT
     *
     * @apiErrorExample {json} Error-Response (405):
     *
     * HTTP/1.1 405 Method Not Allowed
     *   {
     *       "name": "Method Not Allowed",
     *       "message": "Method Not Allowed. This URL can only handle the following request methods: GET.",
     *       "code": 0,
     *       "status": 405,
     *       "type": "yii\\web\\MethodNotAllowedHttpException"
     *   }
     *
     */
    public function actionList(): SuccessResponse
    {
        $userGroup = UserGroup::find()
            ->select(['ug_id', 'ug_key', 'ug_name', 'ug_disable', 'ug_updated_dt'])
            //->where(['ug_disable' => false])
            ->orderBy(['ug_id' => SORT_ASC])
            ->all();

        return new SuccessResponse(
            new DataMessage(
                new Message('user-group', $userGroup)
            )
        );
    }
}
