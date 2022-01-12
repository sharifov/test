<?php

namespace src\services\user\payroll;

use src\model\user\entity\payroll\UserPayroll;
use src\repositories\user\UserPaymentRepository;
use src\repositories\user\UserPayrollRepository;
use src\repositories\user\UserProfitRepository;
use src\services\TransactionManager;
use yii\helpers\ArrayHelper;

/**
 * Class UserPayrollService
 * @package src\services\user\payroll
 *
 * @property UserPayrollRepository $userPayrollRepository
 * @property UserProfitRepository $userProfitRepository
 * @property UserPaymentRepository $userPaymentRepository
 * @property TransactionManager $transactionManager
 */
class UserPayrollService
{
    /**
     * @var UserPayrollRepository
     */
    private $userPayrollRepository;
    /**
     * @var UserProfitRepository
     */
    private $userProfitRepository;
    /**
     * @var UserPaymentRepository
     */
    private $userPaymentRepository;
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function __construct(
        UserPayrollRepository $userPayrollRepository,
        UserProfitRepository $userProfitRepository,
        UserPaymentRepository $userPaymentRepository,
        TransactionManager $transactionManager
    ) {
        $this->userPayrollRepository = $userPayrollRepository;
        $this->userProfitRepository = $userProfitRepository;
        $this->userPaymentRepository = $userPaymentRepository;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param string $date
     * @param int|null $userId
     * @throws \Throwable
     */
    public function calcUserPayrollByYearMonth(string $date, int $userId = null): void
    {
        $this->transactionManager->wrap(function () use ($date, $userId) {
            foreach ($this->userProfitRepository->getDataForCalcUserPayroll($date, $userId) as $profit) {
                $newPayroll = UserPayroll::create((new UserPayrollCreateDTO())->feelByUserPayrollSearch($profit));

                try {
                    $payrollId = $this->userPayrollRepository->save($newPayroll);

                    $this->userProfitRepository->linkPayroll($newPayroll);

                    $payment = $this->userPaymentRepository->findById($profit->payment_id);
                    $payment->upt_payroll_id = $payrollId;
                    $this->userPaymentRepository->save($payment);
                } catch (\RuntimeException $e) {
                    \Yii::warning($e->getMessage(), 'UserPayrollService::calcUserPayrollByYearMonth');
                }
            }
        });
    }

    /**
     * @param string $date
     * @param int|null $userId
     * @return void
     * @throws \Throwable
     */
    public function recalculateUserPayroll(string $date, int $userId = null): void
    {
        $this->transactionManager->wrap(function () use ($date, $userId) {
            foreach ($this->userProfitRepository->getDataForCalcUserPayroll($date, $userId) as $profit) {
                if ($profit->payroll_id) {
                    $userPayroll = $this->userPayrollRepository->findOneById($profit->payroll_id);
                    $userPayroll->recalculate((new UserPayrollCreateDTO())->feelByUserPayrollSearch($profit));
                    $this->userPayrollRepository->save($userPayroll);
                }
            }
        });
    }
}
