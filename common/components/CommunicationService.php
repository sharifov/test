<?php
/**
 * Created
 * User: alexandr
 * Date: 11/21/18
 * Time: 9:05 AM
 */

namespace common\components;

use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
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
 */

class CommunicationService extends Component
{
    public $url;
    public $url2;
    public $username;
    public $password;
    public $request;


    public function init() : void
    {
        parent::init();
        $this->initRequest();
    }


    /**
     * @return bool
     */
    private function initRequest() : bool
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
     * @throws \yii\httpclient\Exception
     */
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []) : Response
    {
        $url = $this->url . $action;

        //$options = ['RETURNTRANSFER' => 1];

        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data);

        if($headers) {
            $this->request->addHeaders($headers);
        }

        $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if($options) {
            $this->request->setOptions($options);
        }

        return $this->request->send();
    }


    /**
     * @param int $project_id
     * @param string $template_type
     * @param string $email_from
     * @param string $email_to
     * @param array $email_data
     * @param string $language
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function mailPreview(int $project_id, string $template_type, string $email_from, string $email_to, array $email_data = [], string $language = 'en-US') : array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['mail']['email_from'] = $email_from;
        $data['mail']['email_to'] = $email_to;
        $data['mail']['type_key'] = $template_type;
        $data['mail']['language_id'] = $language;
        $data['mail']['email_data'] = $email_data;

        if(isset($email_data['email_from_name']) && $email_data['email_from_name']) {
            $data['mail']['email_from_name'] = $email_data['email_from_name'];
        }

        if(isset($email_data['email_to_name']) && $email_data['email_to_name']) {
            $data['mail']['email_to_name'] = $email_data['email_to_name'];
        }

        $response = $this->sendRequest('email/preview', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
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
     * @param string|null $template_type
     * @param string $email_from
     * @param string $email_to
     * @param array $content_data
     * @param array $email_data
     * @param string|null $language
     * @param int $delay
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function mailSend(int $project_id, ?string $template_type, string $email_from, string $email_to, array $content_data = [], array $email_data = [], ?string $language = 'en-US', int $delay = 0) : array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['mail']['eq_email_from'] = $email_from;
        $data['mail']['eq_email_to'] = $email_to;
        $data['mail']['eq_type_key'] = $template_type;
        $data['mail']['eq_language_id'] = $language;
        $data['mail']['eq_email_data'] = $email_data;


        if(isset($content_data['email_from_name']) && $content_data['email_from_name']) {
            $data['mail']['eq_email_from_name'] = $content_data['email_from_name'];
        }

        if(isset($content_data['email_to_name']) && $content_data['email_to_name']) {
            $data['mail']['eq_email_to_name'] = $content_data['email_to_name'];
        }

        if(isset($content_data['email_message_id']) && $content_data['email_message_id']) {
            $data['mail']['eq_email_message_id'] = $content_data['email_message_id'];
        }

        if(isset($content_data['email_body_html'])) {
            $data['mail']['eq_email_body_html'] = $content_data['email_body_html'];
        }

        if(isset($content_data['email_body_text'])) {
            $data['mail']['eq_email_body_text'] = $content_data['email_body_text'];
        }

        if(isset($content_data['email_subject'])) {
            $data['mail']['eq_email_subject'] = $content_data['email_subject'];
        }

        if(isset($content_data['email_reply_to'])) {
            $data['mail']['eq_email_reply_to'] = $content_data['email_reply_to'];
        }

        if(isset($content_data['email_cc'])) {
            $data['mail']['eq_email_cc'] = $content_data['email_cc'];
        }

        if(isset($content_data['email_bcc'])) {
            $data['mail']['eq_email_bcc'] = $content_data['email_bcc'];
        }

        if($delay > 0) {
            $data['mail']['eq_delay'] = $delay;
        }


        $response = $this->sendRequest('email/send', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function mailTypes() : array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        $response = $this->sendRequest('email/template-types', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function mailGetMessages(array $filter = []) : array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        //$data['project'] = 123;

        if(isset($filter['last_dt'])) {
            $data['last_dt'] = date('Y-m-d H:i:s', strtotime($filter['last_dt']));
        }

        if(isset($filter['last_id'])) {
            $data['last_id'] = (int) $filter['last_id'];
        }

        if(isset($filter['limit'])) {
            $data['limit'] = (int) $filter['limit'];
        }

        if(isset($filter['email_list'])) {
            $data['email_list'] = $filter['email_list'];
        }

        if(isset($filter['project_list'])) {
            $data['project_list'] = $filter['project_list'];
        }


        $response = $this->sendRequest('email/inbox', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error('filter: '. VarDumper::dumpAsString($filter)."\r\n". VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::mailGetMessages');
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
     * @throws \yii\httpclient\Exception
     */
    public function smsPreview(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $sms_data = [], ?string $language = 'en-US') : array
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
            if(isset($response->data['data']['response'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function smsSend(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $content_data = [], array $sms_data = [], ?string $language = 'en-US', ?int $delay = 0) : array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['sms']['sq_phone_from'] = $phone_from;
        $data['sms']['sq_phone_to'] = $phone_to;
        $data['sms']['sq_type_key'] = $template_type;
        $data['sms']['sq_language_id'] = $language;
        $data['sms']['sq_sms_data'] = $sms_data;

        if(isset($content_data['sms_text'])) {
            $data['sms']['sq_sms_text'] = $content_data['sms_text'];
        }

        if($delay > 0) {
            $data['sms']['sq_delay'] = $delay;
        }


        $response = $this->sendRequest('sms/send', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function smsTypes() : array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        $response = $this->sendRequest('sms/template-types', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function smsGetMessages(array $filter = []) : array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        $data['project'] = 123;

        if(isset($filter['last_dt'])) {
            $data['last_dt'] = date('Y-m-d H:i:s', strtotime($filter['last_dt']));
        }

        if(isset($filter['last_id'])) {
            $data['last_id'] = (int) $filter['last_id'];
        }

        if(isset($filter['last_n'])) {
            $data['last_n'] = (int) $filter['last_n'];
        }

        if(isset($filter['limit'])) {
            $data['limit'] = (int) $filter['limit'];
        }

        if(isset($filter['phone_list'])) {
            $data['phone_list'] = $filter['phone_list'];
        }

        if(isset($filter['project_list'])) {
            $data['project_list'] = $filter['project_list'];
        }

        $response = $this->sendRequest('sms/inbox', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error('filter: '. VarDumper::dumpAsString($filter)."\r\n". VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::smsGetMessages');
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
     * @throws \yii\httpclient\Exception
     */
    public function callToPhone(int $project_id, string $phone_from, string $from_number, string $phone_to, string $from_name = '', array $options = []) : array
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
            if(isset($response->data['data']['response'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function updateCall(string $sid, array $updateData = []) : array
    {
        $out = ['error' => false, 'data' => []];

        $data['sid'] = $sid;
        $data['data'] = $updateData;

        $response = $this->sendRequest('voice/update-call', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function redirectCall(string $sid, array $data = [], string $callBackUrl = '') : array
    {
        $out = ['error' => false, 'data' => []];

        $data['sid'] = $sid;
        $data['data'] = $data;
        $data['callBackUrl'] = $callBackUrl;

        $response = $this->sendRequest('voice/redirect-call', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
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
     * @param string $username
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function getJwtToken($username = '') : array
    {

        /*'identity' => $token->jt_agent,
        'client'    => $token->jt_agent,
        'token'     => $token->jt_token,
        'expire'    => $token->jt_expire_dt,*/

        $out = ['error' => false, 'data' => []];

        $data['agent'] = $username;

        $response = $this->sendRequest('twilio-jwt/get-token', $data);

        if ($response->isOk) {
            if(isset($response->data['data'])) {
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
     * @throws \yii\httpclient\Exception
     */
    public function getJwtTokenCache($username = '', $deleteCache = false)
    {
        $cacheKey = 'jwt_token_'.$username;

        if($deleteCache) {
            \Yii::$app->cache->delete($cacheKey);
        }
        $out = \Yii::$app->cache->get($cacheKey);

        if ($out === false) {
            $out = $this->getJwtToken($username);

            if($out && isset($out['data']['token']) && $out['data']['token']) {
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
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function callRedirect($cid, $type, $from, $to)
    {

        $out = ['error' => false, 'data' => []];

        $data = [
            'cid' => $cid,
            'type' => $type,
            'redirect_from' => $from,
            'redirect_to' => $to,
        ];

        $response = $this->sendRequest('twilio-jwt/redirect-call', $data);



        if ($response->isOk) {
            // \Yii::warning(VarDumper::dumpAsString(['cid' => $cid, 'type' => $type, 'from' => $from, 'to' => $to, 'content' => $response->data]), 'Component:CommunicationService::callRedirect');
            if(isset($response->data['data'])) {
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
}