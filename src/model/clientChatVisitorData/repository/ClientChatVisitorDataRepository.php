<?php

namespace src\model\clientChatVisitorData\repository;

use src\model\clientChatVisitorData\entity\ClientChatVisitorData;
use src\repositories\NotFoundException;
use yii\helpers\VarDumper;

class ClientChatVisitorDataRepository
{
    public function findOrCreateByVisitorId(string $id): ClientChatVisitorData
    {
        try {
            $visitorData = $this->findByVisitorRcId($id);
        } catch (NotFoundException $e) {
            $visitorData = $this->createByVisitorId($id);
        }
        return $visitorData;
    }

    public function findByVisitorRcId(string $id): ClientChatVisitorData
    {
        if ($visitorData = ClientChatVisitorData::findOne(['cvd_visitor_rc_id' => $id])) {
            return $visitorData;
        }
        throw new NotFoundException('Client Chat Visitor Data not found by cvd_visitor_rc_id: ' . $id);
    }

    public function createByVisitorId(string $id): ClientChatVisitorData
    {
        $visitorData = new ClientChatVisitorData();
        $visitorData->cvd_visitor_rc_id = $id;
        $this->save($visitorData);
        return $visitorData;
    }

    public function createByClientChatRequest(string $visitorRcId, array $data): ClientChatVisitorData
    {
        $visitorData = ClientChatVisitorData::createByClientChatRequest($visitorRcId, $data);
        $visitorData = $this->prepareVisitorData($visitorData);

        try {
            $this->save($visitorData);
        } catch (\RuntimeException $e) {
            \Yii::error('Client Chat Visitor save failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::createByClientChatRequest::save');
        }
        return $visitorData;
    }

    public function prepareVisitorData(ClientChatVisitorData $visitorData): ClientChatVisitorData
    {
        if (!$visitorData->validate()) {
            $errorAttributes = [];
            foreach ($visitorData->errors as $attribute => $error) {
                $errorAttributes[$attribute] = $visitorData->{$attribute};
                $visitorData->{$attribute} = null;
            }
            $errorData = [
                'attributes' => $errorAttributes,
                'errors' => $visitorData->errors,
            ];
            \Yii::error(
                'Client Chat Visitor Data validation failed: ' . VarDumper::dumpAsString($errorData),
                'ClientChatVisitorDataRepository::updateByClientChatRequest::validation'
            );
        }
        return $visitorData;
    }

    public function updateByClientChatRequest(ClientChatVisitorData $visitorData, array $data): void
    {
        $visitorData->fillForUpdateByClientChatRequest($data);
        $visitorData = $this->prepareVisitorData($visitorData);

        try {
            $this->save($visitorData);
        } catch (\RuntimeException $e) {
            \Yii::error('Client Chat Visitor Data save failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::updateByClientChatRequest::save');
        }
    }

    public function existByVisitorRcId(string $id): bool
    {
        return ClientChatVisitorData::find()->byVisitorId($id)->exists();
    }

    public function save(ClientChatVisitorData $clientChatVisitorData): int
    {
        if (!$clientChatVisitorData->save(false)) {
            throw new \RuntimeException('Client Chat Visitor Data saving failed');
        }
        return $clientChatVisitorData->cvd_id;
    }

    public function getOneByChatId(int $id): ClientChatVisitorData
    {
        if ($visitorData = ClientChatVisitorData::find()->joinWithChat($id)->one()) {
            return $visitorData;
        }
        throw new NotFoundException('Chat Visitor Data is not found');
    }
}
