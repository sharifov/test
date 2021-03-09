<?php

namespace common\components;

use common\models\Project;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class HybridService
 *
 * @property string $username
 * @property string $password
 * @property Request $request
 */

class HybridService extends Component
{
    public $username;
    public $password;
    public $request;

    public function init(): void
    {
        parent::init();
        $this->initRequest();
    }

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

    protected function sendRequest(int $projectId, string $action, array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $project = Project::find()->select(['link'])->andWhere(['id' => $projectId])->asArray()->one();
        if (!$project) {
            throw new \DomainException('Not found Project. Id: ' . $projectId);
        }
        if (!$project['link']) {
            throw new \DomainException('Not found link on Project. Id: ' . $projectId);
        }

        $url = $project['link'] . $action;

        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data)
            ->setFormat(Client::FORMAT_JSON);

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
            $metrics->serviceCounter('hybrid', ['type' => 'success', 'action' => $action]);
        } else {
            $metrics->serviceCounter('hybrid', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }

    public function updateStatus($projectId, $orderGid, $statusId): void
    {
        $data = [
            'status' => $statusId,
            'order_id' => $orderGid,
        ];

        $response = $this->sendRequest($projectId, '/offer/v1/order-update-status', $data);

        if ($response->isOk) {
            if (array_keys($response->data['status'])) {
                if ($response->data['status']) {
                    return;
                }
            }
            \Yii::error([
                'message' => 'Not found in response array status key [status]',
                'responseData' => VarDumper::dumpAsString($response->data),
                'requestData' => $data,
            ], 'Component:HybridService::updateStatus');
            throw new \DomainException('Not found in response array status key [status]');
        }

        \Yii::error([
            'message' => 'Not found status response array',
            'responseContent' => $response->content,
            'requestData' => $data,
        ], 'Component:HybridService::updateStatus');
        throw new \DomainException($response->content);
    }
}
