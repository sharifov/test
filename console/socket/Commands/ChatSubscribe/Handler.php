<?php

namespace console\socket\Commands\ChatSubscribe;

use common\models\UserConnection;
use console\socket\Commands\Connectionable;
use console\socket\Commands\ErrorsException;
use console\socket\Commands\Serverable;
use Swoole\WebSocket\Server;

/**
 * Class Handler
 *
 * @property Server|null $server
 * @property int|null $connectionId
 */
class Handler implements Serverable, Connectionable
{
    private ?Server $server = null;
    private ?int $connectionId = null;

    public function handle($params): array
    {
        try {
            [$subscribe, $unSubscribe] = $this->resolveParams($params);

            $userConnection = $this->getUserConnection();
            $originalSubList = $userConnection->getSubList();
            $reallyUnSubscribe = array_intersect($unSubscribe, $originalSubList);
            $newSubList = array_diff($originalSubList, $reallyUnSubscribe);
            $reallySubscribe = array_diff($subscribe, $newSubList);
            $subList = array_merge($newSubList, $reallySubscribe);

            $userConnection->setSubList($subList);
            if (!$userConnection->save()) {
                $errors = $userConnection->getErrors();
                unset($userConnection);
                throw new ErrorsException($errors);
            }
            unset($userConnection);

            $this->subscribe($reallyUnSubscribe);
            $this->unSubscribe($reallySubscribe);

            return [
//                'subList' => $subList,
//                'subscribe' => $reallySubscribe,
//                'unSubscribe' => $reallyUnSubscribe,
            ];
        } catch (ErrorsException $e) {
            $errors = $e->getErrors();
        } catch (\Throwable $e) {
            $errors = [$e->getMessage()];
        }
        unset($e);
        return [
            'command' => 'ClientChatSubscribe',
            'errors' => $errors,
        ];
    }

    private function unSubscribe(array $list): void
    {
        foreach ($list as $value) {
            if (isset($this->server->channelList[$value][$this->connectionId])) {
                unset($this->server->channelList[$value][$this->connectionId]);

                if (isset($this->server->channelList[$value]) && empty($this->server->channelList[$value])) {
                    unset($this->server->channelList[$value]);
                    $this->server->redis->unsubscribe($value);
                }
            }
        }
    }

    private function subscribe(array $list): void
    {
        foreach ($list as $value) {
            $this->server->channelList[$value][$this->connectionId] = $this->connectionId;
            $this->server->redis->subscribe($value);
        }
    }

    private function resolveParams($params): array
    {
        $parameters = new Params();
        $parameters->load($params);
        if (!$parameters->validate()) {
            $errors = $parameters->getErrors();
            unset($parameters);
            throw new ErrorsException($errors);
        }
        if ($parameters->isEmpty()) {
            unset($parameters);
            throw new \DomainException('Params is empty.');
        }

        $values = [$parameters->subscribe, $parameters->unSubscribe];
        unset($parameters);

        return $values;
    }

    private function getUserConnection(): UserConnection
    {
        $userConnection = UserConnection::find()->where(['uc_connection_id' => $this->connectionId])->limit(1)->one();
        if ($userConnection) {
            return $userConnection;
        }
        throw new \DomainException('Not found UserConnection ID: ' . $this->connectionId);
    }

    public function setServer(Server $server): void
    {
        $this->server = $server;
    }

    public function setConnectionId(int $connectionId): void
    {
        $this->connectionId = $connectionId;
    }

    public function __destruct()
    {
        unset($this->server, $this->connectionId);
    }
}
