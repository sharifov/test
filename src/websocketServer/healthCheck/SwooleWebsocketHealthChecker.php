<?php

namespace src\websocketServer\healthCheck;

class SwooleWebsocketHealthChecker
{
    public function check(string $host, int $port, float $timeout): array
    {
        $socket = new \swoole_client(SWOOLE_SOCK_TCP);

        try {
            if (!$socket->connect($host, $port, $timeout)) {
                throw new \RuntimeException('Cant connect to Websocket Server.');
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
            ], 'WebsocketHealthChecker:check:connect');
            throw new \RuntimeException('Cant connect to Websocket Server.');
        }

        $socket->send($this->createHeaders());
        $response = $socket->recv(65535, true);
        $socket->close();

        return $this->processingResponse($response);
    }

    private function processingResponse($response): array
    {
        $parts = explode("\r\n\r\nHTTP/", $response);
        $parts = (count($parts) > 1 ? 'HTTP/' : '') . array_pop($parts);

        [$headers, $body] = explode("\r\n\r\n", $parts, 2);

        if (!$body) {
            throw new \DomainException('Websocket server connected, but body response is empty.');
        }

        $body = $this->hybi10Decode($body);

        try {
            $objectBodyResponse = json_decode($body, true, 3, JSON_THROW_ON_ERROR);
            if ($this->isValidResponse($objectBodyResponse)) {
                return $objectBodyResponse;
            }
            throw new \DomainException('Websocket response is invalid.');
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'wsResponseHeaders' => $headers,
                'jsonBody' => $body,
            ], 'WebsocketHealthChecker:processingResponse');
            throw $e;
        }
    }

    private function isValidResponse(array $response): bool
    {
        return isset($response['ws'], $response['message'], $response['appInstance'], $response['db'], $response['dbSlave']);
    }

    private function createHeaders(): string
    {
        $key = base64_encode(openssl_random_pseudo_bytes(16));
        return "GET / HTTP/1.1" . "\r\n" .
            "Sec-WebSocket-Key: {$key}" . "\r\n" .
            "Upgrade: websocket" . "\r\n" .
            "Connection: Upgrade" . "\r\n" .
            "Health-Check: true" . "\r\n" .
            "Sec-WebSocket-Version: 13" . "\r\n" . "\r\n";
    }

    private function hybi10Decode($data)
    {
        if (empty($data)) {
            return null;
        }

        $bytes = $data;
        $decodedData = '';
        $secondByte = sprintf('%08b', ord($bytes[1]));
        $masked = $secondByte[0] == '1';
        $dataLength = ($masked === true) ? ord($bytes[1]) & 127 : ord($bytes[1]);

        if ($masked === true) {
            if ($dataLength === 126) {
                $mask = substr($bytes, 4, 4);
                $coded_data = substr($bytes, 8);
            } elseif ($dataLength === 127) {
                $mask = substr($bytes, 10, 4);
                $coded_data = substr($bytes, 14);
            } else {
                $mask = substr($bytes, 2, 4);
                $coded_data = substr($bytes, 6);
            }
            for ($i = 0, $iMax = strlen($coded_data); $i < $iMax; $i++) {
                $decodedData .= $coded_data[$i] ^ $mask[$i % 4];
            }
        } else {
            if ($dataLength === 126) {
                $decodedData = substr($bytes, 4, -4);
            } elseif ($dataLength === 127) {
                $decodedData = substr($bytes, 10, -4);
            } else {
                $decodedData = substr($bytes, 2, -4);
            }
        }

        return $decodedData;
    }
}
