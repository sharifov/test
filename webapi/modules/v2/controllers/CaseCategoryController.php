<?php

namespace webapi\modules\v2\controllers;

use sales\entities\cases\CasesCategory;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\SuccessResponse;
use yii\helpers\ArrayHelper;
use yii\filters\HttpCache;

/**
 * Class CaseCategoryController
 */
class CaseCategoryController extends BaseController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
			'HttpCache' => [
				'class' => HttpCache::class,
                'only' => ['list'],
                'lastModified' => static function () {
                    return strtotime(CasesCategory::find()->max('cc_updated_dt'));
                },
			],
		];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return array
     */
    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['list'] = ['GET'];
        return $verbs;
    }

    /**
     * @api {get} /v2/case-category/list Get Case Category
     * @apiVersion 0.2.0
     * @apiName CaseCategoryList
     * @apiGroup CaseCategory
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
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "case-category": [
     *              {
     *                  "cc_key": "add_infant",
     *                  "cc_name": "Add infant",
     *                  "cc_dep_id": 3,
     *                  "cc_system": 0,
     *                  "cc_updated_dt": null
     *              },
     *              {
     *                  "cc_key": "add_insurance",
     *                  "cc_name": "Insurance Add/Remove",
     *                  "cc_dep_id": 3,
     *                  "cc_system": 0,
     *                  "cc_updated_dt": "2019-09-26 15:14:01"
     *              }
     *          ]
     *      },
     *      "technical": {
     *          "action": "v2/case-category/list",
     *          "response_id": 11926631,
     *          "request_dt": "2020-03-16 11:26:34",
     *          "response_dt": "2020-03-16 11:26:34",
     *          "execution_time": 0.076,
     *          "memory_usage": 506728
     *      },
     *      "request": []
     *  }
     *
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
        $casesCategory = CasesCategory::find()
            ->select(['cc_key', 'cc_name', 'cc_dep_id', 'cc_system', 'cc_updated_dt'])
            ->orderBy(['cc_key' => SORT_ASC])
            ->all();

        return new SuccessResponse(
            new DataMessage(
                new Message('case-category', $casesCategory)
            )
        );
    }
}
