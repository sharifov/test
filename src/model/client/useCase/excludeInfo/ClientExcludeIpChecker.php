<?php

namespace src\model\client\useCase\excludeInfo;

use src\helpers\ErrorsToStringHelper;
use src\repositories\client\ClientRepository;
use yii\helpers\VarDumper;

/**
 * Class CheckerClientExcludeIp
 *
 * @property ClientRepository $repository
 */
class ClientExcludeIpChecker
{
    private ClientRepository $repository;

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function check(int $clientId, string $ip): void
    {
        $client = $this->repository->find($clientId);

        $res = \Yii::$app->airsearch->checkExcludeIp($ip);
        if (!$res) {
            throw new \DomainException('Result not found');
        }

        $result = new Result();
        if (!$result->load($res)) {
            throw new \DomainException('Cant load result Data');
        }
        if (!$result->validate()) {
            throw new \DomainException('Result validate Errors: ' . ErrorsToStringHelper::extractFromModel($result, ', '));
        }

        if (!$result->isExcluded()) {
            return;
        }

        $client->exclude($result->ppn);
        $this->repository->save($client);
    }
}
