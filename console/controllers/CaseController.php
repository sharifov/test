<?php

namespace console\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\models\CaseSale;
use common\models\Client;
use common\models\ClientEmail;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\entities\cases\CasesSourceType;
use src\entities\cases\CasesStatus;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\saleTicket\useCase\create\SaleTicketService;
use src\repositories\cases\CasesRepository;
use src\repositories\cases\CasesSaleRepository;
use src\services\cases\CasesManageService;
use src\services\cases\CasesSaleService;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

/**
 * Class CaseController
 *
 * @property CasesRepository $caseRepository
 */
class CaseController extends Controller
{
    private $caseRepository;

    public function __construct($id, $module, CasesRepository $caseRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->caseRepository = $caseRepository;
    }
    /**
     * @param string $importZipName
     * @param string $importFileName
     * @throws Exception
     */
    public function actionImportRefundData(string $importFileName = 'import_refund.json'): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        $runtimePath = '@console/runtime/';

        if (!file_exists(\Yii::getAlias($runtimePath . $importFileName))) {
            throw new Exception('File: ' . $runtimePath . $importFileName . ' is not found');
        }

        if (!preg_match('/\.json$/', $importFileName)) {
            throw new Exception('The imported file must be in json format');
        }

        $refundData = file_get_contents(\Yii::getAlias($runtimePath . $importFileName));

        $refundData = json_decode($refundData, true);

        $totalRows = count($refundData);
        $current = 1;
        foreach ($refundData as $refund) {
            $caseSale = CaseSale::findOne(['css_sale_book_id' => $refund['bookingid']]);
            if (!$caseSale) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $client = Client::create(
                        'ClientName',
                        null,
                        null,
                        $refund['projectid'],
                        Client::TYPE_CREATE_CASE,
                        null,
                        null
                    );
                    if (!$client->save(false)) {
                        throw new Exception($client->getErrorSummary(true)[0]);
                    }
                    $clientEmail = ClientEmail::create($refund['email'], $client->getPrimaryKey(), ClientEmail::EMAIL_NOT_SET);
                    if (!$clientEmail->save()) {
                        throw new Exception($clientEmail->getErrorSummary(true)[0]);
                    }
                    $case = Cases::createExchangeByImport($client->getPrimaryKey(), $refund['projectid'], $refund['bookingid'], $refund['categoryid'], $refund['subject'], CasesSourceType::OTHER);
                    if (!$case->save()) {
                        throw new Exception($case->getErrorSummary(true)[0]);
                    }

                    $job = new CreateSaleFromBOJob();
                    $job->case_id = $case->getPrimaryKey();
                    $job->order_uid = $refund['bookingid'];
                    $job->email = $refund['email'];
                    $job->phone = '';
                    $job->project_key = $case->project->api_key ?? null;
                    Yii::$app->queue_job->priority(100)->push($job);

                    $transaction->commit();

                    echo '----------------' . PHP_EOL;
                    printf(
                        "\n Processed Data: \n ProjectId - %s \n BookingId - %s \n Email: %s \n CategoryId: %s\n Subject: %s \n",
                        $this->ansiFormat($refund['projectid'], Console::FG_GREEN),
                        $this->ansiFormat($refund['bookingid'], Console::FG_GREEN),
                        $this->ansiFormat($refund['email'], Console::FG_GREEN),
                        $this->ansiFormat($refund['categoryid'], Console::FG_GREEN),
                        $this->ansiFormat($refund['subject'], Console::FG_GREEN)
                    );
                    printf(
                        "\n Total Rows: %s \n Current row: %s \n Remaining: %s \n",
                        $this->ansiFormat($totalRows, Console::FG_GREEN),
                        $this->ansiFormat($current, Console::FG_GREEN),
                        $this->ansiFormat($totalRows - $current, Console::FG_GREEN)
                    );
                    echo '----------------' . PHP_EOL;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw new Exception($e->getMessage());
                }
            }
            $current++;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        printf("\nExecute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionRefreshCaseSales($caseId = null, $saleId = null, $limit = 1000, $offset = 0, $showErrorsOnLoop = 1000)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        try {
            $query = new Query();
            $query->addSelect(['cs_id', 'css_sale_id'])->from('cases')->where(['cs_status' => CasesStatus::STATUS_PROCESSING]);

            if ($saleId) {
                $query->innerJoin('case_sale', 'case_sale.css_cs_id = cases.cs_id and case_sale.css_sale_id = :saleId', ['saleId' => $saleId]);
            } else {
                $query->innerJoin('case_sale', 'case_sale.css_cs_id = cases.cs_id');
            }

            if ($caseId) {
                $query->andWhere(['cs_id' => $caseId]);
            }
            $query->limit($limit)->offset($offset);

            $result = $query->all();

            $caseSaleService = Yii::createObject(CasesSaleService::class);
            $saleTicketService = Yii::createObject(SaleTicketService::class);

            $n = 1;
            $total = count($result);
            Console::startProgress(0, $total, 'Counting objects: ', false);

            $boErrors = [];
            $refreshSaleTicketError = [];

            $boRequestTime = 0;
            $serviceTime = 0;

            $caseSaleRepository = Yii::createObject(CasesSaleRepository::class);

            foreach ($result as $item) {
                try {
                    $bo_time_start = microtime(true);
                    $saleData = $caseSaleService->detailRequestToBackOffice((int)$item['css_sale_id'], 0, 120, 1);
                    $bo_time_end = microtime(true);
                    $boRequestTime += (float)number_format(round($bo_time_end - $bo_time_start, 2), 2);

                    $service_time_start = microtime(true);
                    $caseSale = $caseSaleRepository->getSaleByPrimaryKeys((int)$item['cs_id'], (int)$item['css_sale_id']);
                    //              $caseSale = $caseSaleService->refreshOriginalSaleData($caseSale, $case, $saleData);
                    if (!$saleTicketService->refreshSaleTicketBySaleData((int)$item['cs_id'], $caseSale, $saleData)) {
                        $refreshSaleTicketError[] = "Sale {$item['css_sale_id']} doesnt have refund rules;";
                    }
                    $service_time_end = microtime(true);
                    $serviceTime += (float)number_format(round($service_time_end - $service_time_start, 2), 2);
                } catch (BadRequestHttpException $e) {
                    $boErrors[] = "Loop: {$n}; BO error occurred: {$e->getMessage()}; caseId: {$item['cs_id']}; saleId: {$item['css_sale_id']}";
                    $bo_time_end = microtime(true);
                    $boRequestTime += (float)number_format(round($bo_time_end - $bo_time_start, 2), 2);
                } catch (\Throwable $e) {
                    $refreshSaleTicketError[] = "Sale {$item['css_sale_id']} doesnt have refund rules;";
                    $bo_time_end = microtime(true);
                    $boRequestTime += (float)number_format(round($bo_time_end - $bo_time_start, 2), 2);
                }


                if (($n % $showErrorsOnLoop) === 0) {
                    if (!empty($boErrors)) {
                        Console::moveCursorNextLine(2);
                        Console::stdout('Bo errors occurred: ' . PHP_EOL . implode('; ' . PHP_EOL, $boErrors) . PHP_EOL);
                        $boErrors = [];
                    }

                    if (!empty($refreshSaleTicketError)) {
                        Console::moveCursorNextLine(2);
                        Console::stdout('SaleTicketService error occurred: ' . PHP_EOL . implode('; ' . PHP_EOL, $refreshSaleTicketError) . PHP_EOL);
                        $refreshSaleTicketError = [];
                    }

                    Console::moveCursorNextLine(2);
                    print_r($this->ansiFormat('BO request time average: ' . ($boRequestTime / $n) . PHP_EOL, Console::FG_YELLOW));
                    print_r($this->ansiFormat('Service work time average: ' . ($serviceTime / $n) . PHP_EOL, Console::FG_YELLOW));
                    print_r($this->ansiFormat('-------------------------------------------' . PHP_EOL, Console::FG_YELLOW));
                }

                Console::clearLineBeforeCursor();
                Console::updateProgress($n, $total);
                $n++;
            }

            Console::endProgress("done." . PHP_EOL);

            if (!empty($boErrors)) {
                printf("\nBo errors occurred: %s ", $this->ansiFormat(implode('; ' . PHP_EOL, $boErrors), Console::FG_RED));
            }
            if (!empty($refreshSaleTicketError)) {
                printf("\nSaleTicketService error occurred: %s ", $this->ansiFormat(implode('; ' . PHP_EOL, $refreshSaleTicketError), Console::FG_RED));
            }
        } catch (\Throwable $e) {
            printf("\nError occurred: %s ", $this->ansiFormat($e->getMessage() . '; File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), Console::FG_RED));
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        printf("\nExecute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @throws \yii\db\Exception
     */
    public function actionRemoveDuplicate()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);
        $db = Yii::$app->getDb();


        $duplicateCases = $db->createCommand('SELECT DISTINCT(c.cs_id) FROM case_sale as cs
            INNER JOIN cases AS c ON (cs.css_cs_id = c.cs_id)
            WHERE c.cs_status = 1 AND css_sale_id IN (
                SELECT css_sale_id FROM case_sale as cs2
                INNER JOIN cases AS c2 ON (cs2.css_cs_id = c2.cs_id)
                WHERE c2.cs_status = 2
            )')->queryAll();


        if ($duplicateCases) {
            foreach ($duplicateCases as $row) {
                $caseId = (int) $row['cs_id'];
                $case = Cases::findOne($caseId);

                try {
                    Yii::createObject(CasesManageService::class)->trash($case->cs_id, null, 'system:duplicate');
                } catch (\Throwable $throwable) {
                    VarDumper::dump($throwable);
                }

                echo $case->cs_id . "\r\n";
            }
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        printf("\nExecute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionReprotectionClientActiveless()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        $currentTime = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        echo Console::renderColoredString('%g --- CurrentTime: %w[' . $currentTime . '] %n'), PHP_EOL;

        $query = Cases::find()->where(['cs_status' => CasesStatus::STATUS_AUTO_PROCESSING])->joinWith(['category as c']);
        $query->andWhere(['cs_is_automate' => true]);
        $query->andWhere(['<', 'cs_deadline_dt',  $currentTime]);
        $query->andWhere(['c.cc_key' => SettingHelper::getReProtectionCaseCategory()]);
        $cases = $query->all();

        if ($cases) {
            foreach ($cases as $case) {
                try {
                    $case->refresh();
                    if (!$case->isPending()) {
                        $case->pending(null, 'Deadline expired');
                        $case->addEventLog(
                            CaseEventLog::CASE_STATUS_CHANGED,
                            'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: System. Reason: Reprotection client activeless'
                        );
                    }
                    $case->cs_is_automate = false;
                    $this->caseRepository->save($case);
                    echo Console::renderColoredString('%g --- CaseId: %w[' . $case->cs_id . '] %n'), PHP_EOL;
                    echo Console::renderColoredString('%g --- Deadline: %w[' . $case->cs_deadline_dt . '] %n'), PHP_EOL;
                    echo Console::renderColoredString('%g ' . str_repeat('=', 50) . ' %n'), PHP_EOL;
                } catch (\Throwable $e) {
                    $message = AppHelper::throwableLog($e);
                    $message['caseId'] = $case->cs_id;
                    Yii::error($message, 'console:CaseController:actionReprotectionClientActiveless:Case:save');
                }
            }
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        printf("\n --- Execute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionCaseToNeed(): void
    {
        $now = (new \DateTime());
        $report = [];
        $casesQuery = Cases::find()
            ->andWhere(['!=', 'cs_status', CasesStatus::STATUS_PENDING])
            ->andWhere(['!=', 'cs_is_automate', 1])
            ->andWhere(['<', 'cs_deadline_dt', $now->format('Y-m-d H:i:s')])
            ->groupBy('cs_id');
        $count = $casesQuery->count();

        Console::output('Cases to need action...' . PHP_EOL);

        foreach ($casesQuery->batch(100) as $casesBatch) {
            /** @var Cases $case */
            foreach ($casesBatch as $case) {
                try {
                    $case->onNeedAction();
                    $case->save();
                    $report[$case->cs_id] = $item = 'Case: ' . $case->cs_id . ' -> To need action';
                    echo $item . PHP_EOL;
                } catch (\Throwable $exception) {
                    $report[] = $item = 'Case: ' . $case->cs_id . ' not updated';
                    echo $item . PHP_EOL;
                    \Yii::error(
                        AppHelper::throwableLog($exception),
                        'Cases:CaseToNeedAction'
                    );
                }
            }
        }
        echo $count . ' Cases -> offNeedAction' . PHP_EOL;
        $message = '0 cases changed';

        if (count($report) > 0) {
            $message = count($report) . ' cases changed. [' . implode(', ', array_keys($report)) . ']';
        }

        Yii::info($message, 'info\CronCasesToNeedAction');
    }
}
