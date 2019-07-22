<?php

namespace sales\repositories\client;

use common\models\ClientPhone;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class ClientPhoneRepository
 * @method null|ClientPhone get($id)
 * @method null|ClientPhone getByPhone($phone)
 */
class ClientPhoneRepository extends Repository
{
    private $eventDispatcher;

    /**
     * ClientPhoneRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return ClientPhone
     */
    public function find($id): ClientPhone
    {
        if ($phone = ClientPhone::findOne($id)) {
            return $phone;
        }
        throw new NotFoundException('Phone is not found');
    }

    /**
     * @param $phone
     * @return ClientPhone
     */
    public function findByPhone($phone): ClientPhone
    {
        if ($clientPhone = ClientPhone::find()->where(['phone' => $phone])->orderBy(['id' => SORT_DESC])->limit(1)->one()) {
            return $clientPhone;
        }
        throw new NotFoundException('Phone is not found');
    }

    /**
     * @param $clientId
     * @param $phone
     * @return bool
     */
    public function exists($clientId, $phone): bool
    {
        return ClientPhone::find()->where(['client_id' => $clientId, 'phone' => $phone])->exists();
    }

    /**
     * @param ClientPhone $phone
     * @return int
     */
    public function save(ClientPhone $phone): int
    {
        if (!$phone->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($phone->releaseEvents());
        return $phone->id;
    }

    /**
     * @param ClientPhone $phone
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(ClientPhone $phone): void
    {
        if (!$phone->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($phone->releaseEvents());
    }
}