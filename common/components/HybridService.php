<?php

namespace common\components;

use common\components\hybrid\HybridWhData;
use common\models\Project;
use src\helpers\setting\SettingHelper;
use src\model\project\entity\params\Webhook;
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

    protected function sendRequest(string $host, string $action, array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $url = rtrim($host, '/');
        if ($action) {
            $url .= '/' . ltrim($action, '/');
        }

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
            $projectUrls = $this->getProjectUrls($projectId);
            if (!$projectUrls['link']) {
                throw new \DomainException('Not found link on Project. Id: ' . $projectId);
            }

            $webHookOrderHybridEndpoint = SettingHelper::getWebhookOrderUpdateHybridEndpoint();
            if (!$webHookOrderHybridEndpoint) {
                throw new \DomainException('Not webhook order update hybrid endpoint.');
            }

            $response = $this->sendRequest($projectUrls['link'], $webHookOrderHybridEndpoint, $data);

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

    public function wh(int $projectId, string $type, array $data): ?array
    {
        if (!$type) {
            throw new \DomainException('Type is empty.');
        }

        $projectUrls = $this->getProjectUrls($projectId);
        if (!$projectUrls['link']) {
            throw new \DomainException('Not found link on Project. Id: ' . $projectId);
        }
        /** @var Webhook $webhook */
        $webhook = $projectUrls['webhook'];
        if (!$webhook->endpoint) {
            \Yii::warning('Not found webHookEndpoint on Project. Id: ' . $projectId, 'HybridService:wh:webHookEndpoint');
            return null;
        }

        $headers = [];
        if (!empty($webhook->username)) {
            $authStr = base64_encode($webhook->username . ':' . $webhook->password);
            unset($this->request->headers['Authorization']);
            $headers['Authorization'] = 'Basic ' . $authStr;
        }

        $response = $this->sendRequest(
            $projectUrls['link'],
            $webhook->endpoint,
            array_merge(
                ['type' => $type],
                $data
            ),
            'POST',
            $headers,
            []
        );

        if (!$response->isOk) {
            \Yii::error([
                'message' => 'Hybrid Webhook server error',
                'content' => VarDumper::dumpAsString($response->content),
                'type' => $type,
                'data' => $data,
            ], 'Hybrid:wh');
            throw new \DomainException('Hybrid Webhook server error.');
        }

        $data = $response->data;

        if (!$data) {
            \Yii::error([
                'message' => 'Hybrid response Data is empty',
                'content' => VarDumper::dumpAsString($response->content),
                'type' => $type,
                'data' => $data,
            ], 'Hybrid:wh');
            throw new \DomainException('Hybrid response Data is empty.');
        }

        if (!is_array($data)) {
            \Yii::error([
                'message' => 'Hybrid response Data type is invalid',
                'content' => VarDumper::dumpAsString($response->content),
                'type' => $type,
                'data' => $data,
            ], 'Hybrid:wh');
            throw new \DomainException('Hybrid response Data type is invalid.');
        }

        return $data;
    }

    public function whReprotection(int $projectId, array $data): ?array
    {
        return $this->wh($projectId, HybridWhData::WH_TYPE_FLIGHT_SCHEDULE_CHANGE, $data);
    }

    public function whVoluntaryExchangeSuccess(int $projectId, array $data): ?array
    {
        return $this->wh($projectId, 'flight/voluntary-change/create/success', $data);
    }

    public function whVoluntaryExchangeFail(int $projectId, array $data): ?array
    {
        return $this->wh($projectId, 'flight/voluntary-change/create/fail', $data);
    }

    private function getProjectUrls(int $projectId): array
    {
        $project = Project::find()->andWhere(['id' => $projectId])->one();
        if (!$project) {
            throw new \DomainException('Not found Project. Id: ' . $projectId);
        }
        return [
            'link' => $project->link,
            'webhook' => $project->getParams()->webhook
        ];
    }
}
