<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use common\models\Client;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Quote;
use frontend\helpers\JsonHelper;
use src\forms\lead\EmailCreateForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\airportLang\helpers\AirportLangHelper;
use src\repositories\lead\LeadRepository;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use webapi\src\ApiCodeException;
use webapi\src\forms\quote\SendQuoteApiForm;
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
 * Class OfferEmailController
 *
 * @property ClientManageService $clientManageService
 * @property LeadRepository $leadRepository
 */
class OfferEmailController extends ApiBaseController
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
     * @api {post} /v1/offer-email/send-quote Offer email Send Quote
     * @apiVersion 0.1.0
     * @apiName SendQuote
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
     * @apiParam {string{50}}       email_from            Email from
     * @apiParam {string{50}}       email_from_name       Email from name
     * @apiParam {string{50}}       email_to              Email to
     * @apiParam {json}             [additional_data]     Additional data
     * @apiParam {string{5}}        [language_id]         Language Id
     * @apiParam {string{2}}        [market_country_code] Market country code
     *
     * @apiParamExample {json} Request-Example:
         {
            "quote_uid": "60910028642b8",
            "template_key": "cl_offer",
            "email_from": "from@test.com",
            "email_from_name": "Tester",
            "email_to": "to@test.com",
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
     *          "result": "Email sending. Mail ID(427561)"
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
        $sendQuoteApiForm = new SendQuoteApiForm();
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
                'OfferEmailController:actionSendQuote:sendQuoteApiForm'
            );
            return $this->endApiLog(new ErrorResponse(
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

            $emailData = $lead->getEmailData2([$quote->id], $projectContactInfo, $lang, $agent);
            $emailData = ArrayHelper::merge($emailData, $sendQuoteApiForm->additional_data);

            $mailPreview = \Yii::$app->comms->mailPreview(
                $lead->project_id,
                $sendQuoteApiForm->template_key,
                $sendQuoteApiForm->email_from,
                $sendQuoteApiForm->email_to,
                $emailData
            );

            if ($mailPreview['error'] !== false) {
                if (
                    ($responseDecoded = JsonHelper::decode($mailPreview['error'])) &&
                    ArrayHelper::keyExists('message', $responseDecoded)
                ) {
                    $errorsMessage[] = 'Communication error. ' . str_replace('"', "'", $responseDecoded['message']);
                    throw new \DomainException('Communication error. MailPreview Service', ApiCodeException::COMMUNICATION_ERROR);
                }
                throw new \DomainException('Communication error. ' . VarDumper::dumpAsString($mailPreview['error']), ApiCodeException::COMMUNICATION_ERROR);
            }

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $lead->project_id;
            $clientForm->typeCreate = Client::TYPE_CREATE_EMAIL;
            $client = $this->clientManageService->getOrCreateByEmails([new EmailCreateForm(['email' => $sendQuoteApiForm->email_to])], $clientForm);

            $mail = self::createEmail(
                $quote,
                $sendQuoteApiForm->template_key,
                $sendQuoteApiForm->email_from,
                $sendQuoteApiForm->email_from_name,
                $sendQuoteApiForm->email_to,
                $sendQuoteApiForm->language_id,
                $mailPreview['data']['email_subject'],
                $mailPreview['data']['email_body_html'],
                $client->id
            );

            $mailResponse = $mail->sendMail();
            if ($mailResponse['error'] !== false) {
                if (
                    ($responseDecoded = JsonHelper::decode($mailResponse['error'])) &&
                    ArrayHelper::keyExists('message', $responseDecoded)
                ) {
                    $errorsMessage[] = 'Communication error. ' . str_replace('"', "'", $responseDecoded['message']);
                    throw new \DomainException('Email(Id: ' . $mail->e_id . ') has not been sent.', ApiCodeException::COMMUNICATION_ERROR);
                }
                throw new \DomainException('Communication error. ' . VarDumper::dumpAsString($mailResponse['error']), ApiCodeException::COMMUNICATION_ERROR);
            }

            if (!$lead->client_id) {
                $lead->client_id = $client->id;
            }
            $this->leadRepository->save($lead);

            $responseData['result'] = 'Email sending. Mail ID(' . $mail->e_id . ')';
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'OfferEmailController:actionSendQuote:Throwable');
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

    private static function createEmail(
        Quote $quote,
        $templateKey,
        $from,
        $fromName,
        $to,
        $languageId,
        $subject,
        $body,
        $clientId
    ): Email {
        $mail = new Email();
        $mail->e_project_id = $quote->lead->project_id;
        $mail->e_lead_id = $quote->lead_id;

        if ($templateTypeId = self::getTemplateTypeId($templateKey)) {
            $mail->e_template_type_id = (int) $templateTypeId;
        }
        $mail->e_type_id = Email::TYPE_OUTBOX;
        $mail->e_status_id = Email::STATUS_PENDING;
        $mail->e_email_subject = $subject;
        $mail->body_html = $body;
        $mail->e_email_from = $from;
        $mail->e_email_from_name = $fromName;
        $mail->e_language_id = $languageId;
        $mail->e_email_to = $to;
        $mail->e_created_dt = date('Y-m-d H:i:s');
        $mail->e_client_id = $clientId;

        if (!$mail->save()) {
            throw new \DomainException(ErrorsToStringHelper::extractFromModel($mail));
        }

        $mail->e_message_id = $mail->generateMessageId();
        $mail->save();
        return $mail;
    }

    /**
     * @param string $templateKey
     * @return false|int|string|null
     */
    private static function getTemplateTypeId(string $templateKey)
    {
        return EmailTemplateType::find()
            ->select(['etp_id'])
            ->andWhere(['etp_key' => $templateKey])
            ->scalar();
    }

    private function endApiLog(Response $response): Response
    {
        $this->apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }
}
