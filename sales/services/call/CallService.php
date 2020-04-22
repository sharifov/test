<?php

namespace sales\services\call;

use common\models\Call;
use common\models\Notifications;
use common\models\PhoneBlacklist;
use common\models\UserProjectParams;
use frontend\widgets\notification\NotificationMessage;
use sales\repositories\call\CallRepository;
use sales\services\ServiceFinder;
use yii\helpers\VarDumper;

/**
 * Class CallService
 *
 * @property  CallRepository $callRepository
 * @property  ServiceFinder $finder
 */
class CallService
{
    private $callRepository;
    private $finder;

    public function __construct(CallRepository $callRepository, ServiceFinder $finder)
    {
        $this->callRepository = $callRepository;
        $this->finder = $finder;
    }

    /**
     * @param int $callId
     * @param int $userId
     */
    public function cancelByCrash(int $callId, int $userId): void
    {
        if (!$call = Call::findOne(['c_id' => $callId])) {
            throw new \DomainException('Call not found');
        }

        if ($call->isEnded()) {
            throw new \DomainException('Cannot cancel call in current status.');
        }

        if (!$call->isOwner($userId)) {
            throw new \DomainException('You are not owner this call.');
        }

        $call->cancel();
        $this->callRepository->save($call);
    }

    public function guardDeclined(?string $clientPhoneNumber, array $data, int $typeId): void
    {
        if (!$clientPhoneNumber) {
            return;
        }

        $internalPhoneNumber = $data['To'] ?? null;

        if (!$blackPhone = PhoneBlacklist::find()->isExists($clientPhoneNumber)) {
            return;
        }

        $call = Call::createDeclined(
            $data['CallSid'] ?? null,
            $typeId,
            $clientPhoneNumber,
            $internalPhoneNumber,
            date('Y-m-d H:i:s'),
            $data['c_com_call_id'] ?? null,
            Call::getClientTime($data),
            Call::getDisplayRegion($data['FromCountry'] ?? ''),
            $data['FromState'] ?? null,
            $data['FromCity'] ?? null,
            null
        );

        if (!$call->save()) {
            \Yii::error(VarDumper::dumpAsString($call->errors), 'CallService:guardDeclined:Call:save');
            throw new \Exception('CallService:guardDeclined: Can not save call in db', 1);
        }

        if (
//            ($upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $internalPhoneNumber])->limit(1)->one())
            ($upp = UserProjectParams::find()->byPhone($internalPhoneNumber, false)->limit(1)->one())
            && ($user = $upp->uppUser)
        ) {
            if ($ntf = Notifications::create($user->id, 'Declined Call',
                'Declined Call Id: ' . $call->c_id . ' Reason: Blacklisted',
                Notifications::TYPE_WARNING, true)
            ) {
                $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::sendSocket('getNewNotification', ['user_id' => $user->id], $dataNotification);
            }
        }

        throw new CallDeclinedException('Declined Call Id: ' . $call->c_id . '. Reason: Blacklisted');
    }
}
