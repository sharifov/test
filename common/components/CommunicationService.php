<?php

/**
 * Created
 * User: alexandr
 * Date: 11/21/18
 * Time: 9:05 AM
 */

namespace common\components;

use common\models\Conference;
use common\models\ConferenceRoom;
use sales\model\call\useCase\conference\create\CreateCallForm;
use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class CommunicationService
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property Request $request
 * @property string $voipApiUsername
 */

class CommunicationService extends Component implements CommunicationServiceInterface
{
    public $url;
    public $url2;
    public $username;
    public $password;
    public $request;
    public $recording_url = '';
    public $voipApiUsername = '';


    public function init(): void
    {
        parent::init();
        $this->initRequest();
    }


    /**
     * @return bool
     */
    private function initRequest(): bool
    {
        $authStr = base64_encode($this->username . ':' . $this->password);

        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            $this->request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
            return true;
        } catch (\Throwable $error) {
            \Yii::error(VarDumper::dumpAsString($error, 10), 'CommunicationService::initRequest:Exception');
        }

        return false;
    }

    /**
     * @param string $action
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @return \yii\httpclient\Response
     * @throws Exception
     */
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $url = $this->url . $action;

        //$options = ['RETURNTRANSFER' => 1];

        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data);

        if ($headers) {
            $this->request->addHeaders($headers);
        }

        $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if ($options) {
            $this->request->addOptions($options);
        }
        if (isset(Yii::$app->params['additionalCurlOptions'])) {
            $this->request->addOptions(Yii::$app->params['additionalCurlOptions']);
        }

        $response = $this->request->send();

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('communication', ['type' => 'success', 'action' => $action]);
        } else {
            $metrics->serviceCounter('communication', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }


    /**
     * @param int $project_id
     * @param string $template_type
     * @param string $email_from
     * @param string $email_to
     * @param array $email_data
     * @param string $language
     * @return array
     * @throws Exception
     */
    public function mailPreview(int $project_id, string $template_type, string $email_from, string $email_to, array $email_data = [], string $language = 'en-US'): array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['mail']['email_from'] = $email_from;
        $data['mail']['email_to'] = $email_to;
        $data['mail']['type_key'] = $template_type;
        $data['mail']['language_id'] = $language;
        $data['mail']['email_data'] = $email_data;

        if (isset($email_data['email_from_name']) && $email_data['email_from_name']) {
            $data['mail']['email_from_name'] = $email_data['email_from_name'];
        }

        if (isset($email_data['email_to_name']) && $email_data['email_to_name']) {
            $data['mail']['email_to_name'] = $email_data['email_to_name'];
        }

        $response = $this->sendRequest('email/preview', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::mailPreview');
        }

        return $out;
    }

    /**
     * @param int $project_id
     * @param string $template_type
     * @param string $email_from
     * @param string $email_to
     * @param array $email_data
     * @param string $language
     * @param array $capture_options
     * @return array
     * @throws Exception
     */
    public function mailCapture(int $project_id, string $template_type, string $email_from, string $email_to, array $email_data = [], string $language = 'en-US', array $capture_options = []): array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['mail']['email_from'] = $email_from;
        $data['mail']['email_to'] = $email_to;
        $data['mail']['type_key'] = $template_type;
        $data['mail']['language_id'] = $language;
        $data['mail']['email_data'] = $email_data;
        $data['mail']['capture_options'] = $capture_options;

        if (isset($email_data['email_from_name']) && $email_data['email_from_name']) {
            $data['mail']['email_from_name'] = $email_data['email_from_name'];
        }

        if (isset($email_data['email_to_name']) && $email_data['email_to_name']) {
            $data['mail']['email_to_name'] = $email_data['email_to_name'];
        }

        $response = $this->sendRequest('email/capture', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
        }

        if ($out['error']) {
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::mailCapture');
        }

        return $out;
    }

    /**
     * @param int $project_id
     * @param string|null $template_type
     * @param string $email_from
     * @param string $email_to
     * @param array $content_data
     * @param array $email_data
     * @param string|null $language
     * @param int $delay
     * @return array
     * @throws Exception
     */
    public function mailSend(int $project_id, ?string $template_type, string $email_from, string $email_to, array $content_data = [], array $email_data = [], ?string $language = 'en-US', int $delay = 0): array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['mail']['eq_email_from'] = $email_from;
        $data['mail']['eq_email_to'] = $email_to;
        $data['mail']['eq_type_key'] = $template_type;
        $data['mail']['eq_language_id'] = $language;
        $data['mail']['eq_email_data'] = $email_data;


        if (isset($content_data['email_from_name']) && $content_data['email_from_name']) {
            $data['mail']['eq_email_from_name'] = $content_data['email_from_name'];
        }

        if (isset($content_data['email_to_name']) && $content_data['email_to_name']) {
            $data['mail']['eq_email_to_name'] = $content_data['email_to_name'];
        }

        if (isset($content_data['email_message_id']) && $content_data['email_message_id']) {
            $data['mail']['eq_email_message_id'] = $content_data['email_message_id'];
        }

        if (isset($content_data['email_body_html'])) {
            $data['mail']['eq_email_body_html'] = $content_data['email_body_html'];
        }

        if (isset($content_data['email_body_text'])) {
            $data['mail']['eq_email_body_text'] = $content_data['email_body_text'];
        }

        if (isset($content_data['email_subject'])) {
            $data['mail']['eq_email_subject'] = $content_data['email_subject'];
        }

        if (isset($content_data['email_reply_to'])) {
            $data['mail']['eq_email_reply_to'] = $content_data['email_reply_to'];
        }

        if (isset($content_data['email_cc'])) {
            $data['mail']['eq_email_cc'] = $content_data['email_cc'];
        }

        if (isset($content_data['email_bcc'])) {
            $data['mail']['eq_email_bcc'] = $content_data['email_bcc'];
        }

        if ($delay > 0) {
            $data['mail']['eq_delay'] = $delay;
        }


        $response = $this->sendRequest('email/send', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::mailSend');
        }

        return $out;
    }


    /**
     * @return array
     * @throws Exception
     */
    public function mailTypes(): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        $response = $this->sendRequest('email/template-types', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::mailTypes');
        }

        return $out;
    }


    /**
     * @param array $filter
     * @return array
     * @throws Exception
     */
    public function mailGetMessages(array $filter = []): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        //$data['project'] = 123;

        if (isset($filter['last_dt'])) {
            $data['last_dt'] = date('Y-m-d H:i:s', strtotime($filter['last_dt']));
        }

        if (isset($filter['last_id'])) {
            $data['last_id'] = (int) $filter['last_id'];
        }

        if (isset($filter['limit'])) {
            $data['limit'] = (int) $filter['limit'];
        }

        if (isset($filter['email_list'])) {
            $data['email_list'] = $filter['email_list'];
        }

        if (isset($filter['project_list'])) {
            $data['project_list'] = $filter['project_list'];
        }


        $response = $this->sendRequest('email/inbox', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error('filter: ' . VarDumper::dumpAsString($filter) . "\r\n" . VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::mailGetMessages');
        }

        return $out;
    }


    /**
     * @param int $project_id
     * @param string|null $template_type
     * @param string $phone_from
     * @param string $phone_to
     * @param array $sms_data
     * @param string|null $language
     * @return array
     * @throws Exception
     */
    public function smsPreview(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $sms_data = [], ?string $language = 'en-US'): array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['sms']['phone_from'] = $phone_from;
        $data['sms']['phone_to'] = $phone_to;
        $data['sms']['type_key'] = $template_type;
        $data['sms']['language_id'] = $language;
        $data['sms']['sms_data'] = $sms_data;

        $response = $this->sendRequest('sms/preview', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::smsPreview');
        }

        return $out;
    }


    /**
     * @param int $project_id
     * @param string $template_type
     * @param string $phone_from
     * @param string $phone_to
     * @param array $content_data
     * @param array $sms_data
     * @param string|null $language
     * @param int|null $delay
     * @return array
     * @throws Exception
     */
    public function smsSend(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $content_data = [], array $sms_data = [], ?string $language = 'en-US', ?int $delay = 0): array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['sms']['sq_phone_from'] = $phone_from;
        $data['sms']['sq_phone_to'] = $phone_to;
        $data['sms']['sq_type_key'] = $template_type;
        $data['sms']['sq_language_id'] = $language;
        $data['sms']['sq_sms_data'] = $sms_data;

        if (isset($content_data['sms_text'])) {
            $data['sms']['sq_sms_text'] = $content_data['sms_text'];
        }

        if ($delay > 0) {
            $data['sms']['sq_delay'] = $delay;
        }


        $response = $this->sendRequest('sms/send', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::smsSend');
        }

        return $out;
    }


    /**
     * @return array
     * @throws Exception
     */
    public function smsTypes(): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        $response = $this->sendRequest('sms/template-types', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::smsTypes');
        }

        return $out;
    }


    /**
     * @param array $filter
     * @return array
     * @throws Exception
     */
    public function smsGetMessages(array $filter = []): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        $data['project'] = 123;

        if (isset($filter['last_dt'])) {
            $data['last_dt'] = date('Y-m-d H:i:s', strtotime($filter['last_dt']));
        }

        if (isset($filter['last_id'])) {
            $data['last_id'] = (int) $filter['last_id'];
        }

        if (isset($filter['last_n'])) {
            $data['last_n'] = (int) $filter['last_n'];
        }

        if (isset($filter['limit'])) {
            $data['limit'] = (int) $filter['limit'];
        }

        if (isset($filter['phone_list'])) {
            $data['phone_list'] = $filter['phone_list'];
        }

        if (isset($filter['project_list'])) {
            $data['project_list'] = $filter['project_list'];
        }

        $response = $this->sendRequest('sms/inbox', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error('filter: ' . VarDumper::dumpAsString($filter) . "\r\n" . VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::smsGetMessages');
        }

        return $out;
    }


    /**
     * @param int $project_id
     * @param string $phone_from
     * @param string $from_number
     * @param string $phone_to
     * @param string $from_name
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function callToPhone(int $project_id, string $phone_from, string $from_number, string $phone_to, string $from_name = '', array $options = []): array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['voice']['from'] = $phone_from;
        $data['voice']['to'] = $phone_to;
        $data['voice']['from_number'] = $from_number;
        $data['voice']['from_name'] = $from_name;

        $data['voice']['options'] = $options; //['url'] = 'http://api-sales.dev.travelinsides.com/v1/twilio/request/?phone=+37369594567*/';


        $response = $this->sendRequest('voice/call-to-phone', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error']), 'Component:CommunicationService::callToPhone');
        }

        return $out;
    }


    /**
     * @param string $sid
     * @param array $updateData
     * @return array
     * @throws Exception
     */
    public function updateCall(string $sid, array $updateData = []): array
    {
        $out = ['error' => false, 'data' => []];

        $data['sid'] = $sid;
        $data['data'] = $updateData;

        $response = $this->sendRequest('voice/update-call', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error']), 'Component:CommunicationService::updateCall');
        }

        return $out;
    }


    /**
     * @param string $sid
     * @param array $data
     * @param string $callBackUrl
     * @return array
     * @throws Exception
     */
    public function redirectCall(string $sid, array $data = [], string $callBackUrl = ''): array
    {
        $out = ['error' => false, 'data' => []];

        $data['sid'] = $sid;
        $data['data'] = $data;
        $data['callBackUrl'] = $callBackUrl;

        $response = $this->sendRequest('voice/redirect-call', $data);

        if ($response->isOk) {
            if (isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error']), 'Component:CommunicationService::updateCallTunnel');
        }

        return $out;
    }

    /**
     * @param string $sid
     * @param string $status (paused|in-progress|stopped)
     * @param string|null $recordingSid
     * @return array
     * @throws Exception
     */
    public function updateRecordingStatus(string $sid, string $status, ?string $recordingSid = null): array
    {
        $out = ['error' => false, 'data' => []];

        $data['sid'] = $sid;
        $data['status'] = $status;
        if ($recordingSid) {
            $data['recording_sid'] = $recordingSid;
        }

        /*$response = $this->sendRequest('voice/update-recording-status', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];

                if (isset($response->data['error'])) {
                    \Yii::error(VarDumper::dumpAsString($out['error']),
                    'CommunicationService::updateRecordingStatus:errorResponse');
                } else {
                    \Yii::info('Call sid: ' . $sid . ' set record status to: ' . $status,
                    'info\CommunicationService:updateRecordingStatus');
                }
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error']),
            'CommunicationService::updateRecordingStatus:failRequest');
        }*/

        return $out;
    }


    /**
     * @param string $username
     * @return array
     * @throws Exception
     */
    public function getJwtToken($username = ''): array
    {

        /*'identity' => $token->jt_agent,
        'client'    => $token->jt_agent,
        'token'     => $token->jt_token,
        'expire'    => $token->jt_expire_dt,*/

        $out = ['error' => false, 'data' => []];

        $data['agent'] = $username;

        $response = $this->sendRequest('twilio-jwt/get-token', $data);

        if ($response->isOk) {
            if (isset($response->data['data'])) {
                $out['data'] = $response->data['data'];
            } else {
                $out['error'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error']), 'Component:CommunicationService::getToken');
        }

        return $out;
    }


    /**
     * @param string $username
     * @param bool $deleteCache
     * @return mixed
     * @throws Exception
     */
    public function getJwtTokenCache($username = '', $deleteCache = false)
    {
        $cacheKey = 'jwt_token_' . $username;

        if ($deleteCache) {
            \Yii::$app->cache->delete($cacheKey);
        }
        $out = \Yii::$app->cache->get($cacheKey);

        if ($out === false) {
            $out = $this->getJwtToken($username);

            if ($out && isset($out['data']['token']) && $out['data']['token']) {
                $expired = isset($out['data']['expire']) ? strtotime($out['data']['expire']) - time() : 60 * 30;
                \Yii::$app->cache->set($cacheKey, $out, $expired);
            }
        }

        return $out;
    }

    /**
     * @param $cid
     * @param $type
     * @param $from
     * @param $to
     * @param  bool $firstTransferToNumber
     * @return array
     * @throws Exception
     */
    public function callRedirect($cid, $type, $from, $to, $firstTransferToNumber = false)
    {
        $out = ['error' => false, 'data' => []];

        $data = [
            'cid' => $cid,
            'type' => $type,
            'redirect_from' => $from,
            'redirect_to' => $to,
            'firstTransferToNumber' => $firstTransferToNumber
        ];

        $response = $this->sendRequest('twilio-jwt/redirect-call', $data);



        if ($response->isOk) {
            // \Yii::warning(VarDumper::dumpAsString(['cid' => $cid, 'type' => $type, 'from' => $from, 'to' => $to, 'content' => $response->data]), 'Component:CommunicationService::callRedirect');
            if (isset($response->data['data'])) {
                $out['data'] = $response->data['data'];
            } else {
                $out['error'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error']), 'Component:CommunicationService::callRedirect');
        }

        return $out;
    }

    public function callForward($sid, $from, $to): array
    {
        $data = [
            'sid' => $sid,
            'from' => $from,
            'to' => $to,
        ];

        $response = $this->sendRequest('twilio-conference/forward', $data);

        return $this->processConferenceResponse($response);
    }

    public function acceptConferenceCall($id, $sid, $to, $from, $userId): array
    {
        $data = [
            'call_id' => $id,
            'call_sid' => $sid,
            'to' => $to,
            'from' => $from,
            'user_id' => $userId
        ];

        $response = $this->sendRequest('twilio-conference/accept-call', $data);

        return $this->processConferenceResponse($response);
    }

    public function hangUp(string $sid): array
    {
        $data = [
            'sid' => $sid,
        ];

        $response = $this->sendRequest('twilio-conference/hangup', $data);

        return $this->processConferenceResponse($response);
    }

    public function cancelCall(string $sid): array
    {
        $data = [
            'sid' => $sid,
        ];

        $response = $this->sendRequest('twilio-conference/cancel-call', $data);

        return $this->processConferenceResponse($response);
    }

    public function holdConferenceCall(string $conferenceSid, string $keeperSid): array
    {
        $data = [
            'conferenceSid' => $conferenceSid,
            'keeperSid' => $keeperSid,
        ];

        $response = $this->sendRequest('twilio-conference/hold-call', $data);

        return $this->processConferenceResponse($response);
    }

    public function unholdConferenceCall(string $conferenceSid, string $keeperSid): array
    {
        $data = [
            'conferenceSid' => $conferenceSid,
            'keeperSid' => $keeperSid,
        ];

        $response = $this->sendRequest('twilio-conference/unhold-call', $data);

        return $this->processConferenceResponse($response);
    }

    public function disconnectFromConferenceCall(string $conferenceSid, string $keeperSid): array
    {
        $data = [
            'conferenceSid' => $conferenceSid,
            'keeperSid' => $keeperSid,
        ];

        $response = $this->sendRequest('twilio-conference/disconnect-from-conference-call', $data);

        return $this->processConferenceResponse($response);
    }

    public function returnToConferenceCall(
        string $callSid,
        string $parentCallSid,
        string $friendlyName,
        string $conferenceSid,
        string $to,
        int $userId
    ): array {
        $data = [
            'callSid' => $callSid,
            'parentCallSid' => $parentCallSid,
            'friendlyName' => $friendlyName,
            'conferenceSid' => $conferenceSid,
            'to' => $to,
            'user_id' => $userId,
            'voipApiUsername' => $this->voipApiUsername,
        ];

        $response = $this->sendRequest('twilio-conference/return-to-conference-call', $data);

        return $this->processConferenceResponse($response);
    }

    public function muteParticipant(string $conferenceSid, string $callSid): array
    {
        $data = [
            'conferenceSid' => $conferenceSid,
            'callSid' => $callSid,
        ];

        $response = $this->sendRequest('twilio-conference/mute-participant', $data);

        return $this->processConferenceResponse($response);
    }

    public function unmuteParticipant(string $conferenceSid, string $callSid): array
    {
        $data = [
            'conferenceSid' => $conferenceSid,
            'callSid' => $callSid,
        ];

        $response = $this->sendRequest('twilio-conference/unmute-participant', $data);

        return $this->processConferenceResponse($response);
    }

    public function joinToConference(string $callSid, string $conferenceSid, int $projectId, string $from, string $to, string $source_type_id, int $user_id): array
    {
        $data = [
            'callSid' => $callSid,
            'conferenceSid' => $conferenceSid,
            'projectId' => $projectId,
            'from' => $from,
            'to' => $to,
            'source_type_id' => $source_type_id,
            'user_id' => $user_id
        ];

        $response = $this->sendRequest('twilio-conference/join-to-conference', $data);

        return $this->processConferenceResponse($response);
    }

    public function createCall(CreateCallForm $form): array
    {
        $response = $this->sendRequest('twilio-conference/create-call', $form->getAttributes());

        return $this->processConferenceResponse($response);
    }

    private function processConferenceResponse(\yii\httpclient\Response $response): array
    {
        return $this->processResponse($response);
    }

    private function processResponse(\yii\httpclient\Response $response): array
    {
        $out = ['error' => false, 'message' => '', 'result' => []];

        if ($response->isOk) {
            if (isset($response->data['data'])) {
                $data = $response->data['data'];
                $isError = (bool)($data['is_error'] ?? false);
                if ($isError) {
                    $out['error'] = true;
                    $out['message'] = (string)($data['message'] ?? 'Undefined error message');
                    if (
                        !
                        (
                            (!empty($data['code']) && $data['code'] === 21220 && $out['message'] === 'Call status is Completed')
                            || (!empty($data['code']) && $data['code'] === 20404 && $out['message'] === 'Send digit error. Conference not found')
                        )
                    ) {
                        \Yii::error(VarDumper::dumpAsString($response->data), 'Component:CommunicationService::processResponse:response');
                    }
                }
                $out['result'] = $data['result'] ?? [];
            } else {
                $out['error'] = true;
                $out['message'] = 'Not found in response array data';
            }
        } else {
            $out['error'] = true;
            $out['message'] = 'Server error. Try again later.';
            \Yii::error(VarDumper::dumpAsString($response->content), 'Component:CommunicationService::processResponse');
        }

        return $out;
    }

    private function processResponseGetPrice(\yii\httpclient\Response $response): array
    {
        $notFoundCode = 20404;//twilio not found code

        $out = ['error' => false, 'message' => '', 'result' => []];

        if ($response->isOk) {
            if (isset($response->data['data'])) {
                $code = $response->data['code'] ?? null;
                $data = $response->data['data'];
                $isError = (bool)($data['is_error'] ?? false);
                if ($isError && $code !== $notFoundCode) {
                    $out['error'] = true;
                    $out['message'] = (string)($data['message'] ?? 'Undefined error message');
                    \Yii::error(VarDumper::dumpAsString($response->data), 'Component:CommunicationService::processResponse:response');
                }
                if ($code === $notFoundCode) {
                    $out['message'] = (string)($data['message'] ?? 'Undefined message');
                }
                $out['result'] = $data['result'] ?? [];
            } else {
                $out['error'] = true;
                $out['message'] = 'Not found in response array data';
            }
        } else {
            $out['error'] = true;
            $out['message'] = 'Server error. Try again later.';
            \Yii::error(VarDumper::dumpAsString($response->content), 'Component:CommunicationService::processResponse');
        }

        return $out;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws Exception
     */
    public function phoneNumberList(int $limit = 0, int $offset = 0): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];

        if ($limit) {
            $data['limit'] = $limit;
        }

        if ($offset) {
            $data['offset'] = $offset;
        }

        $response = $this->sendRequest('phone-number/list', $data, 'get');

        if ($response->isOk) {
            if (empty($response->data['data']['response'])) {
                $out['error'] = 'Not found in response array data key [data]';
            } else {
                $out['data'] = $response->data['data']['response'];
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error']), 'Component:CommunicationService::callRedirect');
        }

        return $out;
    }

    public function sendDigitToConference(string $conferenceSid, string $digit): array
    {
        $data = [
            'conferenceSid' => $conferenceSid,
            'digit' => $digit,
        ];

        $response = $this->sendRequest('twilio-conference/send-digit', $data);

        return $this->processConferenceResponse($response);
    }

    public function getCallPrice(string $callSid): array
    {
        $data = [
            'callSid' => $callSid,
        ];

        $response = $this->sendRequest('twilio/get-call-price', $data);

        return $this->processResponseGetPrice($response);
    }

    public function getSmsPrice(string $smsSid): array
    {
        $data = [
            'smsSid' => $smsSid,
        ];

        $response = $this->sendRequest('twilio/get-sms-price', $data);

        return $this->processResponseGetPrice($response);
    }

    public function callToUser(string $from, string $to, int $to_user_id, int $created_userId, array $requestCall, string $friendly_name): array
    {
        $data = [
            'from' => $from,
            'to' => $to,
            'to_user_id' => $to_user_id,
            'created_user_id' => $created_userId,
            'requestCall' => $requestCall,
            'voipApiUsername' => $this->voipApiUsername,
            'friendly_name' => $friendly_name,
        ];

        $response = $this->sendRequest('twilio-conference/call-to-user', $data);

        return $this->processResponse($response);
    }

    public function getCallInfo(string $callSid): array
    {
        $data = [
            'callSid' => $callSid,
        ];

        $response = $this->sendRequest('twilio-conference/get-call-info', $data);

        return $this->processResponse($response);
    }

    public function repeatMessage(array $data): array
    {
        $response = $this->sendRequest('twilio/repeat-message', $data);

        $out = [
            'code' => null,
            'error' => false,
            'message' => '',
            'result' => []
        ];

        if ($response->isOk) {
            if (isset($response->data['data'])) {
                $data = $response->data['data'];
                $isError = (bool)($data['is_error'] ?? false);
                if ($isError) {
                    $out['error'] = true;
                    $out['message'] = (string)($data['message'] ?? 'Undefined error message');
                }
                $out['result'] = $data['result'] ?? [];
                $out['code'] = $response->data['code'] ?? [];
            } else {
                $out['error'] = true;
                $out['message'] = 'Not found in response array data';
            }
        } else {
            $out['error'] = true;
            $out['message'] = 'Server error. Try again later.';
            \Yii::error(VarDumper::dumpAsString($response->content), 'Component:CommunicationService::repeatMessage');
        }

        return $out;
    }
}
