<?php
namespace console\controllers;

use common\models\ProfitSplit;
use sales\model\user\entity\payment\UserPayment;
use sales\model\user\entity\profit\search\UserProfitSearch;
use sales\model\user\entity\profit\UserProfit;
use sales\services\user\payroll\UserPayrollService;
use yii\console\Controller;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

/**
 * Class UserPayrollController
 * @package console\controllers
 *
 * @property UserPayrollService $userPayrollService
 */
class UserPayrollController extends Controller
{
	/**
	 * @var UserPayrollService
	 */
	private $userPayrollService;

	public function __construct($id, $module, UserPayrollService $userPayrollService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->userPayrollService = $userPayrollService;
	}

	/**
	 * generate test data for user_profit table based on leads table
	 * @throws \Exception
	 */
	public function actionFeelUserProfitTable()
	{
		$splitProfit = ProfitSplit::find()->where(['>=', 'date(ps_updated_dt)', '2019-01-01']);

		printf("\n --- Start: total row that will be generated - %s ---\n", $this->ansiFormat($splitProfit->count(), Console::FG_YELLOW));

		foreach ($splitProfit->all() as $key => $profit) {
			$userProfit = new UserProfit();

			$userProfit->up_user_id = $profit->ps_user_id;
			$userProfit->up_percent = $profit->psUser->userParams->up_commission_percent;
			$userProfit->up_lead_id = $profit->ps_lead_id;
			$userProfit->up_split_percent = $profit->ps_percent;
			$userProfit->up_profit = random_int(0, 300);
			$userProfit->up_created_dt = $profit->ps_updated_dt;
			$userProfit->up_updated_dt = $profit->ps_updated_dt;

			$userProfit->save();

			if ($key % 50 === 0) {
				printf("\n --- Count of generated rows - %s ---\n", $this->ansiFormat($key, Console::FG_YELLOW));
			}
		}

		printf("\n --- Finish ---\n");
	}

	/**
	 * generate test data for user_payment table based on user_profit table
	 *
	 * @throws \Exception
	 */
	public function actionFeelUserPaymentTable(): void
	{
		$userProfit = UserProfit::find();

		printf("\n --- Start: total row that will be generated - %s ---\n", $this->ansiFormat($userProfit->count(), Console::FG_YELLOW));

		foreach ($userProfit->all() as $key => $profit) {
			$payment = new UserPayment();

			$categoryId = random_int(1,3);
			$payment->upt_assigned_user_id = $profit->up_user_id;
			$payment->upt_category_id = $categoryId;
			$payment->upt_status_id = random_int(1,4);
			$payment->upt_amount = $categoryId !== 2 ? random_int(0,100) : -random_int(0,100);
			$payment->upt_date = date('Y-m-d', strtotime($profit->up_created_dt));

			$payment->save();

			if ($key % 50 === 0) {
				printf("\n --- Count of generated rows - %s ---\n", $this->ansiFormat($key, Console::FG_YELLOW));
			}
		}

		printf("\n --- Finish ---\n");
	}

	/**
	 * Calculate payroll for all users stored in user_profit and user_payment
	 * @throws \Throwable
	 */
	public function actionCalcUserPayrollPreviousMonth(): void
	{
		$date = BaseConsole::input('Enter date in format yyyy-mm: ');

		if (!empty($date) && !preg_match('/^\d{4}-\d{2}$/', $date)) {
			print($this->ansiFormat("\n --- Date format is not valid; ---\n", Console::FG_RED));
			exit;
		}

		$userId = BaseConsole::input('Enter user id: ');

		if (!empty($userId) && !preg_match('/^\d+$/', $userId)) {
			print($this->ansiFormat("\n --- User Id must be an integer; ---\n", Console::FG_RED));
			exit;
		}

		print($this->ansiFormat("\n --- Start ---\n", Console::FG_YELLOW));

		$date = $date ?? date('Y-m', strtotime('-1 month'));

		try {
			$this->userPayrollService->calcUserPayrollByYearMonth($date, $userId ?: null);
		} catch (\RuntimeException $e) {
			\Yii::error($e->getMessage(), 'Console::UserPayrollController::actionCalcUserPayrollPreviousMonth::RuntimeException');
			print($this->ansiFormat("\n --- ".$e->getMessage()." ---\n", Console::FG_RED));
			exit;
		} catch (\Throwable $e) {
			\Yii::error($e->getMessage(), 'Console::UserPayrollController::actionCalcUserPayrollPreviousMonth::Throwable');
			print($this->ansiFormat("\n --- Some errors has occurred; Check system log; ---\n", Console::FG_RED));
			exit;
		}

		print($this->ansiFormat("\n --- Finish ---\n", Console::FG_GREEN));
	}

	/**
	 * Recalculate user payroll
	 */
	public function actionRecalculateUserPayroll(): void
	{
		$date = BaseConsole::input('Enter date in format yyyy-mm: ');

		if (!empty($date) && !preg_match('/^\d{4}-\d{2}$/', $date)) {
			print($this->ansiFormat("\n --- Date format is not valid; ---\n", Console::FG_RED));
			exit;
		}

		$userId = BaseConsole::input('Enter user id: ');

		if (!empty($userId) && !preg_match('/^\d+$/', $userId)) {
			print($this->ansiFormat("\n --- User Id must be an integer; ---\n", Console::FG_RED));
			exit;
		}

		$date = $date ?? date('Y-m', strtotime('-1 month'));

		print($this->ansiFormat("\n --- Start ---\n", Console::FG_YELLOW));

		try {
			$this->userPayrollService->recalculateUserPayroll($date, $userId ?: null);
		} catch (\RuntimeException $e) {
			\Yii::error($e->getMessage(), 'Console::UserPayrollController::actionRecalculateUserPayroll::RuntimeException');
			print($this->ansiFormat("\n --- ".$e->getMessage()." ---\n", Console::FG_RED));
			exit;
		} catch (\Throwable $e) {
			\Yii::error($e->getMessage(), 'Console::UserPayrollController::actionRecalculateUserPayroll::Throwable');
			print($this->ansiFormat("\n --- Some errors has occurred; Check system log; ---\n", Console::FG_RED));
			exit;
		}

		print($this->ansiFormat("\n --- Finish ---\n", Console::FG_GREEN));
	}
}