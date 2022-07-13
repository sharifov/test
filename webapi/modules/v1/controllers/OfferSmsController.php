<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use common\models\Client;
use common\models\Sms;
use common\models\SmsTemplateType;
use frontend\helpers\JsonHelper;
use frontend\models\LeadPreviewSmsForm;
use src\forms\lead\PhoneCreateForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\airportLang\helpers\AirportLangHelper;
use src\repositories\lead\LeadRepository;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use webapi\src\ApiCodeException;
use webapi\src\forms\quote\SendSmsQuoteApiForm;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class OfferSmsController
 *
 * @property ClientManageService $clientManageService
 * @property LeadRepository $leadRepository
 */
class OfferSmsController extends ApiBaseController
{
    private ClientManageService $clientManageService;
    private LeadRepository $leadRepository;

    /**
     * @param $id
     * @param $module
     * @param ClientManageService $clientManageService
     * @param LeadRepository $leadRepository
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ClientManageService $clientManageService,
        LeadRepository $leadRepository,
        $config = []
    ) {
        $this->clientManageService = $clientManageService;
        $this->leadRepository = $leadRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * @api {post} /v1/offer-sms/send-quote Offer sms Send Quote
     * @apiVersion 0.1.0
     * @apiName SendSmsQuote
     * @apiGroup Quotes
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{13}}       quote_uid             Quote UID
     * @apiParam {string{50}}       template_key          Template key
     * @apiParam {string{50}}       sms_from              Sms from
     * @apiParam {string{50}}       sms_to                Sms to
     * @apiParam {json}             [additional_data]     Additional data
     * @apiParam {string{5}}        [language_id]         Language Id
     * @apiParam {string{2}}        [market_country_code] Market country code
     *
     * @apiParamExample {json} Request-Example:
         {
            "quote_uid": "60910028642b8",
            "template_key": "sms_client_offer",
            "sms_from": "+16082175601",
            "sms_to": "+16082175602",
            "language_id": "en-US",
            "market_country_code": "RU",
            "additional_data": [
                {
                    "code": "PR",
                    "airline": "Philippine Airlines"
                }
            ]
         }
     *
     * @apiSuccessExample {json} Success-Response:
     *   HTTP/1.1 200 OK
     *   {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "result": "Sms sending. Mail ID(427561)"
     *      }
     *   }
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *   {
     *      "status": 422,
     *      "message": "Validation error",
     *      "errors": {
     *          "quote_uid": [
     *              "Quote not found by Uid(60910028642b1)"
     *          ]
     *      },
     *      "code": 0
     *   }
     *
     * @apiErrorExample {json} Error-Response (400):
     *   HTTP/1.1 400 Bad Request
     *   {
     *      "name": "Bad Request",
     *      "message": "POST data request is empty",
     *      "code": 2,
     *      "status": 400,
     *      "type": "yii\\web\\BadRequestHttpException"
     *   }
     */
    public function actionSendQuote()
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);
        $post = Yii::$app->request->post();
        $responseData = [];
        $sendQuoteApiForm = new SendSmsQuoteApiForm();
        $errorsMessage = [];

        if (!$sendQuoteApiForm->load($post)) {
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR)
            ));
        }
        if (!$sendQuoteApiForm->validate()) {
            \Yii::warning(
                ErrorsToStringHelper::extractFromModel($sendQuoteApiForm),
                'OfferSmsController:actionSendQuote:sendQuoteApiForm'
            );
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($sendQuoteApiForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            ));
        }

        $quote = $sendQuoteApiForm->getQuote();
        $lead = $quote->lead;

        $lang = null;
        if ($sendQuoteApiForm->language_id) {
            $lang = AirportLangHelper::getLangFromLocale($sendQuoteApiForm->language_id);
        }
        $agent['full_name'] = '';
        $agent['username'] = '';
        $agent['nickname'] = '';

        try {
            $projectContactInfo = [];
            if ($lead->project && $lead->project->contact_info) {
                $projectContactInfo = JsonHelper::decode($lead->project->contact_info);
            }
            $contentData = $lead->getEmailData2([$quote->id], $projectContactInfo, $lang, $agent);
            $contentData = ArrayHelper::merge($contentData, $sendQuoteApiForm->additional_data);

            $smsPreview = Yii::$app->comms->smsPreview(
                $lead->project_id,
                $sendQuoteApiForm->template_key,
                $sendQuoteApiForm->sms_from,
                $sendQuoteApiForm->sms_to,
                $contentData,
                $sendQuoteApiForm->language_id
            );

            if ($smsPreview['error'] !== false) {
                if (
                    ($smsResponseDecoded = JsonHelper::decode($smsPreview['error'])) &&
                    ArrayHelper::keyExists('message', $smsResponseDecoded)
                ) {
                    $errorsMessage[] = 'Communication error. ' . str_replace('"', "'", $smsResponseDecoded['message']);
                    throw new \DomainException('Communication error. SmsPreview Service', ApiCodeException::COMMUNICATION_ERROR);
                }
                throw new \DomainException(VarDumper::dumpAsString($smsPreview['error']), ApiCodeException::COMMUNICATION_ERROR);
            }

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $lead->project_id;
            $clientForm->typeCreate = Client::TYPE_CREATE_SMS;
            $client = $this->clientManageService->getOrCreateByPhones([new PhoneCreateForm(['phone' => $sendQuoteApiForm->sms_to])], $clientForm);

            $sms = new Sms();
            $sms->s_project_id = $lead->project_id;
            $sms->s_lead_id = $lead->id;
            if ($templateTypeId = self::getTemplateTypeId($sendQuoteApiForm->template_key)) {
                $sms->s_template_type_id = (int) $templateTypeId;
            }
            $sms->s_type_id = Sms::TYPE_OUTBOX;
            $sms->s_status_id = Sms::STATUS_PENDING;

            $sms->s_sms_text = $smsPreview['data']['sms_text'];
            $sms->s_phone_from = $sendQuoteApiForm->sms_from;
            $sms->s_phone_to = $sendQuoteApiForm->sms_to;

            if ($sendQuoteApiForm->language_id) {
                $sms->s_language_id = $sendQuoteApiForm->language_id;
            }

            $sms->s_created_dt = date('Y-m-d H:i:s');
            $sms->s_created_user_id = null;

            if (!$sms->save()) {
                $errorsMessage[] = $sms->getErrors();
                throw new \DomainException('Sms model saving is failed', ApiCodeException::FAILED_FORM_VALIDATE);
            }

            $smsResponse = $sms->sendSms();
            if (isset($smsResponse['error']) && $smsResponse['error']) {
                if (
                    ($smsResponseDecoded = JsonHelper::decode($smsResponse['error'])) &&
                    ArrayHelper::keyExists('message', $smsResponseDecoded)
                ) {
                    $errorsMessage[] = 'Communication error. ' . str_replace('"', "'", $smsResponseDecoded['message']);
                    throw new \DomainException('Communication error. SendSms Service', ApiCodeException::COMMUNICATION_ERROR);
                }
                throw new \DomainException(VarDumper::dumpAsString($smsResponse['error']), ApiCodeException::COMMUNICATION_ERROR);
            }

            if (!$lead->client_id) {
                $lead->client_id = $client->id;
            }
            $this->leadRepository->save($lead);

            $responseData['result'] = 'Sms sending. Sms ID(' . $sms->s_id . ')';
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'OfferSmsController:actionSendQuote:Throwable');
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
                new ErrorsMessage($errorsMessage),
                new CodeMessage($throwable->getCode())
            ));
        }

        return $this->endApiLog(new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage('OK'),
            new DataMessage($responseData)
        ));
    }

    /**
     * @param string $templateKey
     * @return false|int|string|null
     */
    private static function getTemplateTypeId(string $templateKey)
    {
        return SmsTemplateType::find()
            ->select(['stp_id'])
            ->andWhere(['stp_key' => $templateKey])
            ->scalar();
    }

    private function endApiLog(Response $response): Response
    {
        $this->apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }
}
