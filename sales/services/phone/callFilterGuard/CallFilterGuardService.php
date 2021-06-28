<?php

namespace sales\services\phone\callFilterGuard;

use DateTime;
use sales\helpers\app\AppHelper;
use sales\services\departmentPhoneProject\DepartmentPhoneProjectParamsService;
use sales\services\phone\callFilterGuard\CheckServiceInterface;
use Yii;

/**
 * Class CallFilterGuardService
 *
 * @property string $phone
 * @property DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService
 * @property int $trustPercent
 */
class CallFilterGuardService
{
    private const CLASS_POSTFIX = 'CallFilterGuard';

    private string $phone;
    private DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService;
    private int $trustPercent = 0;

    /**
     * @param string $phone
     * @param DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService
     */
    public function __construct(string $phone, DepartmentPhoneProjectParamsService $departmentPhoneProjectParamsService)
    {
        $this->phone = $phone;
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
}
