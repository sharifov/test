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

        $response = $this->sendRequest('email/preview', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'CommunicationService::mailPreview');
        }

        return $out;
    }

    /**
     * @param int $project_id
     * @param string $template_type
     * @param string $email_from
     * @param string $email_to
     * @param array $content_data
     * @param array $email_data
     * @param string $language
     * @param int $delay
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function mailSend(int $project_id, string $template_type, string $email_from, string $email_to, array $content_data = [], array $email_data = [], string $language = 'en-US', int $delay = 0) : array
    {
        $out = ['error' => false, 'data' => []];

        $data['project_id'] = $project_id;
        $data['mail']['eq_email_from'] = $email_from;
        $data['mail']['eq_email_to'] = $email_to;
        $data['mail']['eq_type_key'] = $template_type;
        $data['mail']['eq_language_id'] = $language;
        $data['mail']['eq_email_data'] = $email_data;

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
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'CommunicationService::mailSend');
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
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'CommunicationService::mailTypes');
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
        $data['project'] = 123;

        if(isset($filter['last_dt'])) {
            $data['last_dt'] = date('Y-m-d H:i:s', strtotime($filter['last_dt']));
        }

        if(isset($filter['last_id'])) {
            $data['last_id'] = (int) $filter['last_id'];
        }


        /*$email_to = Yii::$app->request->post('email_to');
        $email_from = Yii::$app->request->post('email_from');
        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');
        $new = Yii::$app->request->post('new');
        $last_id = Yii::$app->request->post('last_id');
        $last_dt = Yii::$app->request->post('last_dt');*/

        $response = $this->sendRequest('email/inbox', $data);

        if ($response->isOk) {
            if(isset($response->data['data']['response'])) {
                $out['data'] = $response->data['data']['response'];
            } else {
                $out['error'] = 'Not found in response array data key [data][response]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error('filter: '. VarDumper::dumpAsString($filter)."\r\n". VarDumper::dumpAsString($out['error'], 10), 'CommunicationService::mailGetMessages');
        }

        return $out;
    }



}