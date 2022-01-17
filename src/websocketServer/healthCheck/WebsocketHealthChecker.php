<?php

namespace src\websocketServer\healthCheck;

class WebsocketHealthChecker
{
    public function check(string $uri, int $timeout, string $ping): array
    {
        $client = new \WebSocket\Client($uri, [
            'headers' => [
                'Health-Check' => true,
            ],
            'timeout' => $timeout,
        ]);

        $serverHealth = [];
        $pong = [];

        try {
            $serverHealthResponse = $client->receive();
            $serverHealth = $this->processingServerHealth($serverHealthResponse);
            $client->text(json_encode(['ping' => $ping]));
            $pingResponse = $client->receive();
            $pong = $this->processingPingResponse($pingResponse);
        } catch (\Throwable $e) {
            $client->close();
            \Yii::error([
                'message' => $e->getMessage(),
            ], 'WebsocketHealthChecker:check');
            throw $e;
        }
        $client->close();

        return array_merge($serverHealth, $pong);
    }

    private function processingPingResponse($body): array
    {
        try {
            $response = json_decode($body, true, 3, JSON_THROW_ON_ERROR);
            if (isset($response['pong'], $response['appInstance'])) {
                return [
                    'ping' => $response,
                ];
            }
            throw new \DomainException('Websocket ping response is invalid.');
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'body' => $body,
            ], 'WebsocketHealthChecker:processingPingResponse');
            throw $e;
        }
    }

    private function processingServerHealth($body): array
    {
        try {
            $response = json_decode($body, true, 3, JSON_THROW_ON_ERROR);
            if (isset($response['ws'], $response['message'], $response['appInstance'], $response['db'], $response['dbSlave'])) {
                return $response;
            }
            throw new \DomainException('Websocket server health response is invalid.');
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'body' => $body,
            ], 'WebsocketHealthChecker:processingServerHealth');
            throw $e;
        }
    }
}
