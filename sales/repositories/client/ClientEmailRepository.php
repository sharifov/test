<?php

namespace sales\repositories\client;

use common\models\ClientEmail;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

class ClientEmailRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return ClientEmail
     */
    public function get($id): ClientEmail
    {
        if ($clientEmail = ClientEmail::findOne($id)) {
            return $clientEmail;
        }
        throw new NotFoundException('Email is not found');
    }

    /**
     * @param $email
     * @return ClientEmail
     */
    public function getByEmail($email): ClientEmail
    {
        if ($clientEmail = ClientEmail::find()->where(['email' => $email])->orderBy(['id' => SORT_ASC])->limit(1)->one()) {
            return $clientEmail;
        }
        throw new NotFoundException('Email is not found');
    }

    /**
     * @param $clientId
     * @param $email
     * @return bool
     */
    public function exists($clientId, $email): bool
    {
        return ClientEmail::find()->where(['client_id' => $clientId, 'email' => $email])->exists();
    }

    /**
     * @param ClientEmail $email
     * @return int
     */
    public function save(ClientEmail $email): int
    {
        if (!$email->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($email->releaseEvents());
        return $email->id;
    }

    /**
     * @param ClientEmail $email
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(ClientEmail $email): void
    {
        if (!$email->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($email->releaseEvents());
    }
}