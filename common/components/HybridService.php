<?php

namespace common\components;

use common\models\Project;
use sales\helpers\setting\SettingHelper;
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
 * @property string $webHookEndpoint
 */

class HybridService extends Component
{
    public $username;
    public $password;
    public $request;
    public $webHookEndpoint;

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

        $url = rtrim($project['link'], '/') . '/' . ltrim($action, '/');

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

        if (SettingHelper::isWebhookOrderUpdateHybridEnabled()) {
            $response = $this->sendRequest($projectId, SettingHelper::getWebhookOrderUpdateHybridEndpoint(), $data);

            if ($response->isOk) {
                if (array_key_exists('status', $response->data)) {
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

    public function wh(int $projectId, string $type, array $data): array
    {
        if (!$type) {
            throw new \DomainException('Type is empty.');
        }

        if (!$this->webHookEndpoint) {
            throw new \DomainException('Not isset settings hybrid.webHookEndpoint');
        }

        $response = $this->sendRequest(
            $projectId,
            $this->webHookEndpoint,
            array_merge(
                ['type' => $type],
                $data
            ),
            'POST',
            [],
            []
        );

        if (!$response->isOk) {
            \Yii::error([
                'message' => 'Hybrid Webhook server error',
                'type' => $type,
                'data' => $data,
                'content' => VarDumper::dumpAsString($response->content),
            ], 'Hybrid:wh');
            throw new \DomainException('Hybrid Webhook server error.');
        }

        $data = $response->data;

        if (!$data) {
            \Yii::error([
                'message' => 'Hybrid response Data is empty',
                'type' => $type,
                'data' => $data,
                'content' => VarDumper::dumpAsString($response->content),
            ], 'Hybrid:wh');
            throw new \DomainException('Hybrid response Data is empty.');
        }

        if (!is_array($data)) {
            \Yii::error([
                'message' => 'Hybrid response Data type is invalid',
                'type' => $type,
                'data' => $data,
                'content' => VarDumper::dumpAsString($response->content),
            ], 'Hybrid:wh');
            throw new \DomainException('Hybrid response Data type is invalid.');
        }

        return $data;
    }

    public function whReprotection(int $projectId, array $data): array
    {
        return $this->wh($projectId, 'reprotection', $data);
    }
}
