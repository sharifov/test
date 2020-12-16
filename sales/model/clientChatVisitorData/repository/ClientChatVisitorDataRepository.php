<?php

namespace sales\model\clientChatVisitorData\repository;

use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;
use yii\helpers\VarDumper;

class ClientChatVisitorDataRepository extends Repository
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
        if (!$visitorData->validate()) {
            foreach ($visitorData->errors as $attribute => $error) {
                $visitorData->{$attribute} = null;
            }
            \Yii::error('Client Chat Visitor validation failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::createByClientChatRequest::validation');
        }

        try {
            $this->save($visitorData);
        } catch (\RuntimeException $e) {
            \Yii::error('Client Chat Visitor save failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::createByClientChatRequest::save');
        }
        return $visitorData;
    }

    public function updateByClientChatRequest(ClientChatVisitorData $visitorData, array $data): void
    {
        $visitorData->updateByClientChatRequest($data);
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
