<?php

namespace sales\services\phone\callFilterGuard;

use common\models\Call;
use DateTime;
use PHPStan\BetterReflection\Reflector\Exception\IdentifierNotFound;
use sales\helpers\app\AppHelper;
use sales\model\contactPhoneData\entity\ContactPhoneData;
use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\services\call\CallDeclinedException;
use sales\services\call\CallService;
use sales\services\departmentPhoneProject\DepartmentPhoneProjectParamsService;
use sales\services\phone\blackList\PhoneBlackListManageService;
use sales\services\phone\callFilterGuard\CheckServiceInterface;
use sales\services\phone\checkPhone\CheckPhoneService;
use Twilio\TwiML\VoiceResponse;
use Yii;

/**
 * Class CallFilterGuardService
 *
 * @property string $phone
 * @property int $trustPercent
 *
 * @property DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService
 * @property CallService $callService
 */
class CallFilterGuardService
{
    private const CLASS_POSTFIX = 'CallFilterGuard';

    private string $phone;
    private int $trustPercent = 0;

    private DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService;
    private CallService $callService;

    /**
     * @param string $phone
     * @param DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService
     * @param CallService $callService
     */
    public function __construct(
        string $phone,
        DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService,
        CallService $callService
    ) {
        $this->phone = $phone;
        $this->callService = $callService;
        $this->departmentPhoneProjectParamsService = $departmentPhoneProjectParamsService;

        if ($this->isEnable()) {
            $this->calculateTrustPercent();
        }
    }

    private function calculateTrustPercent(): CallFilterGuardService
    {
        $trustPercents = [];
        $services = $this->departmentPhoneProjectParamsService->getCallFilterGuardTrustCheckService();

        foreach ($services as $checkServiceName) {
            /** @var CheckServiceInterface $checkService */
            try {
                $checkService = $this->initServiceClass($checkServiceName)->default();
                $trustPercents[] = $checkService->getTrustPercent();
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableLog($throwable),
                    'CallFilterGuardService:calculateTrustPercent:Throwable'
                );
            }
        }
        if ($services) {
            $this->trustPercent = array_sum($trustPercents) / count($services);
        }

        return $this;
    }

    private function initServiceClass(string $checkServiceName): CheckServiceInterface
    {
        $preparedClassName = ucfirst(trim($checkServiceName));
        $nameClass = __NAMESPACE__ . '\\' . $preparedClassName . self::CLASS_POSTFIX;

        if (class_exists($nameClass)) {
            return new $nameClass($this->getPhone());
        }
        throw new \DomainException('Class(' . $preparedClassName . ') not found');
    }

    public function isTrusted(): bool
    {
        if ($this->checkByContactPhoneData()) {
            return true;
        }
        return ($this->getTrustPercent() >= $this->departmentPhoneProjectParamsService->getCallFilterGuardPercent());
    }

    public function isEnable(): bool
    {
        if (!$this->departmentPhoneProjectParamsService->getCallFilterGuardEnable()) {
            return false;
        }
        if (
            ($enabledFromDt = $this->departmentPhoneProjectParamsService->getCallFilterGuardEnabledFromDt()) &&
            new DateTime() < new DateTime($enabledFromDt)
        ) {
            return false;
        }
        if (
            ($enabledToDt = $this->departmentPhoneProjectParamsService->getCallFilterGuardEnabledToDt()) &&
            new DateTime() > new DateTime($enabledToDt)
        ) {
            return false;
        }
        return true;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getTrustPercent(): int
    {
        return $this->trustPercent;
    }

    public function runRepression(array $postCall): void
    {
        if ($this->departmentPhoneProjectParamsService->getCallFilterGuardCallTerminate()) {
            throw new CallDeclinedException('Phone number(' . $this->getPhone() . ') is terminated. Reason - CallFilterGuardTrust');
        }
        if ($this->departmentPhoneProjectParamsService->getCallFilterGuardBlockListEnabled()) {
            $addMinutes = $this->departmentPhoneProjectParamsService->getCallFilterGuardBlockListExpiredMinutes();
            PhoneBlackListManageService::createOrRenewExpiration(
                $this->getPhone(),
                $addMinutes,
                new \DateTime(),
                'Reason - CallFilterGuardTrust'
            );
            $this->callService->guardDeclined($this->getPhone(), $postCall, Call::CALL_TYPE_IN);
        }
    }

    public function checkByContactPhoneData(): bool
    {
        return ContactPhoneList::find()
            ->innerJoin(ContactPhoneData::tableName(), 'cpl_id = cpd_cpl_id')
            ->where(['cpl_uid' => CheckPhoneService::uidGenerator($this->phone)])
            ->andWhere(['IN', 'cpd_key', [ContactPhoneDataDictionary::KEY_IS_TRUSTED, ContactPhoneDataDictionary::KEY_ALLOW_LIST]])
            ->andWhere(['cpd_value' => '1'])
            ->exists();
    }

    public static function getResponseChownData(VoiceResponse $vr, int $status = 200, int $code = 0, string $message = ''): array
    {
        $response['twml'] = (string) $vr;
        return [
            'status' => $status,
            'name' => ($status === 200 ? 'Success' : 'Error'),
            'code' => $code,
            'message' => $message,
            'data' => ['response' => $response]
        ];
    }
}
