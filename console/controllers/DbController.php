<?php

namespace console\controllers;

use common\models\Airline;
use common\models\ClientPhone;
use common\models\DbDataSensitive;
use common\models\DbDataSensitiveView;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\Employee;
use common\models\GlobalLog;
use common\models\Lead;
use common\models\LeadFlow;
//use common\models\LeadLog;
use common\models\LeadQcall;
use common\models\Project;
use common\models\ProjectWeight;
use common\models\Quote;
use common\models\UserProjectParams;
use frontend\helpers\JsonHelper;
use modules\requestControl\models\UserSiteActivity;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\helpers\email\TextConvertingHelper;
use src\logger\db\GlobalLogInterface;
use src\logger\db\LogDTO;
use src\model\project\entity\projectLocale\ProjectLocale;
use src\repositories\NotFoundException;
use src\services\dbDataSensitive\DbDataSensitiveService;
use src\services\lead\qcall\CalculateDateService;
use src\services\log\GlobalEntityAttributeFormatServiceService;
use src\services\system\DbViewCryptDictionary;
use src\services\system\DbViewCryptService;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use src\model\airline\service\AirlineService;
use src\model\dbDataSensitive\dictionary\DbDataSensitiveDictionary;
use src\model\dbDataSensitive\repository\DbDataSensitiveRepository;

/**
 * Class DbController
 * @package console\controllers
 *
 * @property GlobalEntityAttributeFormatServiceService $globalLogFormatAttrService
 */
class DbController extends Controller
{
    private const MODELS_PATH = [
        '\\common\\models\\',
        '\\frontend\\models\\'
    ];

    /** @var GlobalEntityAttributeFormatServiceService  */
    private $globalLogFormatAttrService;

    /** @var DbDataSensitiveService  */
    private DbDataSensitiveService $dbDataSensitiveService;

    /** @var DbDataSensitiveRepository  */
    private DbDataSensitiveRepository $dbDataSensitiveRepository;

    /**
     * DbController constructor.
     * @param $id
     * @param $module
     * @param GlobalEntityAttributeFormatServiceService $globalLogFormatAttrService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        GlobalEntityAttributeFormatServiceService $globalLogFormatAttrService,
        DbDataSensitiveService $dbDataSensitiveService,
        DbDataSensitiveRepository $dbDataSensitiveRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->globalLogFormatAttrService = $globalLogFormatAttrService;
        $this->dbDataSensitiveService = $dbDataSensitiveService;
        $this->dbDataSensitiveRepository = $dbDataSensitiveRepository;
    }

    public function actionUpdateCaseLastAction()
    {
        printf("\n --- Update last action ---\n");
        $count = 0;
        $cases = Cases::find()->select(['cs_updated_dt', 'cs_id'])->andWhere(['IS', 'cs_last_action_dt', null])->asArray()->all();
        $counts = count($cases);
        foreach ($cases as $index => $case) {
            $count++;
            if ($count === 1000) {
                $count = 0;
                printf("\n --- Left: %s Cases ---\n", $this->ansiFormat(($counts - $index), Console::FG_YELLOW));
            }
            Cases::updateAll(['cs_last_action_dt' => $case['cs_updated_dt']], 'cs_last_action_dt IS NULL AND cs_id = ' . $case['cs_id']);
        }
        printf("\n --- Done ---\n");
    }

    public function actionInitProjectWeight()
    {
        if (((int)ProjectWeight::find()->count()) > 0) {
            return 'Table not empty';
        }
        $projects = Project::find()->select(['id'])->indexBy('id')->asArray()->all();

        Yii::$app->db->createCommand()->batchInsert('{{%project_weight}}', ['pw_project_id'], $projects)->execute();
    }

    public function actionSendEmptyDepartmentLeadToSales()
    {
        $leads = Lead::updateAll(['l_dep_id' => Department::DEPARTMENT_SALES], ['IS', 'l_dep_id', null]);
        printf("\n --- Sent %s Leads ---\n", $this->ansiFormat($leads, Console::FG_YELLOW));
    }

    public function actionRemoveClientSystemPhones()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $systemPhones = [];

        foreach (DepartmentPhoneProject::find()->select(['dpp_phone_number'])->asArray()->all() as $phone) {
            if ($phone['dpp_phone_number']) {
                $systemPhones[$phone['dpp_phone_number']] = $phone['dpp_phone_number'];
            }
        }

        $countDepartmentPhoneProject = count($systemPhones);

        printf("\n --- Found %s phones from DepartmentPhoneProject ---\n", $this->ansiFormat($countDepartmentPhoneProject, Console::FG_YELLOW));

        foreach (UserProjectParams::find()->select(['upp_phone_number', 'upp_tw_phone_number'])->asArray()->all() as $phone) {
            if ($phone['upp_phone_number']) {
                $systemPhones[$phone['upp_phone_number']] = $phone['upp_phone_number'];
            }
            if ($phone['upp_tw_phone_number']) {
                $systemPhones[$phone['upp_tw_phone_number']] = $phone['upp_tw_phone_number'];
            }
        }

        $countUserProjectParams = count($systemPhones) - $countDepartmentPhoneProject;

        printf("\n --- Found %s phones from UserProjectParams ---\n", $this->ansiFormat($countUserProjectParams, Console::FG_YELLOW));

        $countClientPhones = ClientPhone::find()->andWhere(['phone' => $systemPhones])->count();

        printf("\n --- Found %s phones from ClientPhones ---\n", $this->ansiFormat($countClientPhones, Console::FG_YELLOW));

        $countDeleted = ClientPhone::deleteAll(['phone' => $systemPhones]);

        printf("\n --- Deleted %s ClientPhones ---\n", $this->ansiFormat($countDeleted, Console::FG_YELLOW));

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    //todo may be deprecated. questions
    public function actionLeadQcall()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $leads = Lead::find()
            ->andWhere(['status' => 1])
            ->andWhere(['NOT IN', 'id', (new Query())->select('lqc_lead_id')->from(LeadQcall::tableName())])
            ->all();
        foreach ($leads as $lead) {
            $lq = new LeadQcall();
            $lq->lqc_lead_id = $lead->id;
            $lq->lqc_weight = 0;
            $lq->lqc_created_dt = $lead->created;

            $lq->lqc_dt_from = date('Y-m-d H:i:s');
            $lq->lqc_dt_to = date('Y-m-d H:i:s', strtotime('+3 days'));

            if (!$lq->save()) {
                Yii::error(VarDumper::dumpAsString($lq->errors), 'Lead:createOrUpdateQCall:LeadQcall:save');
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


    /**
     * @throws \yii\db\Exception
     */
    public function actionConvertCollate()
    {
        $collate = 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';

        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables
        $tables = $db->createCommand('SELECT table_name, table_collation FROM information_schema.tables WHERE table_schema=:schema AND table_type = "BASE TABLE" AND table_collation <> :collation', [
            ':schema' => $schema,
            ':collation' => $collation,
        ])->queryAll();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();

        //VarDumper::dump($tables); exit;

        // Alter the encoding of each table
        foreach ($tables as $id => $table) {
            $tableName = $table['TABLE_NAME'];
            if ($tableName) {
                $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET " . $collate . " COLLATE " . $collation)->execute();
                echo $id . " - tbl: " . $tableName . " - " . $table['TABLE_COLLATION'] . " \r\n";
            }
        }
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @param string $tableName
     * @throws Exception
     */
    public function actionConvertCollateTbl(string $tableName)
    {
        $collate = 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';

        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        echo ' - Table: "' . $tableName . '"';
        //echo PHP_EOL;

        $db = Yii::$app->getDb();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET " . $collate . " COLLATE " . $collation)->execute();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


    /**
     * Remove Client Emails and Phones Duplicates
     */
    public function actionRemoveClientDuplicates()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();

        $duplicatesEmail = $db->createCommand('SELECT ce.id, count(client_id) as cnt, email, client_id FROM client_email ce GROUP BY email, client_id HAVING cnt > 1')->queryAll();

        if (count($duplicatesEmail) > 0) {
            foreach ($duplicatesEmail as $entry) {
                $db->createCommand('DELETE FROM client_email WHERE email = :email AND client_id = :client_id AND id != :id', [
                    'id' => $entry['id'],
                    'email' => $entry['email'],
                    'client_id' => $entry['client_id']
                ])->execute();
            }
            printf("\n--- Removed %s duplicates in client_email ---\n", count($duplicatesEmail));
        } else {
            printf("\n--- In client_email not found duplicates ---\n");
        }

        $duplicatesPhone = $db->createCommand('SELECT ce.id, count(client_id) as cnt, phone, client_id FROM client_phone ce GROUP BY phone, client_id HAVING cnt > 1')->queryAll();

        if (count($duplicatesPhone) > 0) {
            foreach ($duplicatesPhone as $entry) {
                $db->createCommand('DELETE FROM client_phone WHERE phone = :phone AND client_id = :client_id AND id != :id', [
                    'id' => $entry['id'],
                    'phone' => $entry['phone'],
                    'client_id' => $entry['client_id']
                ])->execute();
            }
            printf("\n--- Removed %s duplicates in client_phone ---\n", count($duplicatesPhone));
        } else {
            printf("\n--- In client_phone not found duplicates ---\n");
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * Update Airline cabin classes from airapi
     * 30   0  *  *  *     run-this-one php /var/www/sale/yii db/update-airline-cabin-classes
     */
    public function actionUpdateAirlineCabinClasses()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        //Airline::syncCabinClasses();
        AirlineService::synchronization();

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


    /**
     * @throws \yii\db\Exception
     */
    public function actionUpdateLeadFlow()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();


        $sql = 'SELECT id,
(SELECT lf1.status FROM lead_flow AS lf1 WHERE lf1.lead_id = lf.lead_id AND lf1.id < lf.id ORDER BY lf1.id DESC LIMIT 1) AS from_status_id,
(SELECT lf2.created FROM lead_flow AS lf2 WHERE lf2.lead_id = lf.lead_id AND lf2.id > lf.id ORDER BY lf2.id ASC LIMIT 1) AS end_dt,
(UNIX_TIMESTAMP((SELECT lf2.created FROM lead_flow AS lf2 WHERE lf2.lead_id = lf.lead_id AND lf2.id > lf.id ORDER BY lf2.id ASC LIMIT 1)) - UNIX_TIMESTAMP(lf.created)) AS time_duration
FROM lead_flow AS lf
ORDER BY lf.lead_id, id';

        $logs = $db->createCommand($sql)->queryAll();

        if ($logs) {
            foreach ($logs as $nr => $log) {
                LeadFlow::updateAll(['lf_from_status_id' => $log['from_status_id'], 'lf_end_dt' => $log['end_dt'], 'lf_time_duration' => $log['time_duration']], ['id' => $log['id']]);
                echo $nr . ' - id: ' . $log['id'] . "\n";
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * Update quotes from dump to trip + segments
     */
    public function actionUpdateQuotesFromDump()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $quotes = Quote::find()->leftJoin('quote_trip', 'quote_trip.qt_quote_id = quotes.id')->where(['quote_trip.qt_id' => null])->all();
        printf("\n Quotes to update: %d \n", count($quotes));
        if (count($quotes)) {
            $cntUpdated = 0;
            foreach ($quotes as $quote) {
                if ($quote->createQuoteTrips()) {
                    $cntUpdated++;
                }
            }

            printf("\n Quotes updated: %d \n", $cntUpdated);
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionUpdateSoldSales()
    {
        /**
         * @var $leadsFlow LeadFlow[]
         */
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();

        $leadIds = ArrayHelper::map(Lead::find()->where(['status' => Lead::STATUS_SOLD])->all(), 'id', 'id');

        $leadsFlow = LeadFlow::findAll([
            'lead_id' => $leadIds,
            'status' => Lead::STATUS_SOLD
        ]);

        foreach ($leadsFlow as $leadFlow) {
            $sql = sprintf('UPDATE leads SET updated = \'%s\' WHERE id = %d', $leadFlow->created, $leadFlow->lead_id);
            $db->createCommand($sql)->execute();

            printf("\n SQL - %s\n", $sql);
            printf("\n Lead ID: %d  - updated: '%s'\n", $leadFlow->lead_id, $leadFlow->created);
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


    public function actionTelegramWebhook()
    {
        /**
         * @var $leadsFlow LeadFlow[]
         */
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $url = Yii::$app->params['telegram']['webhook_url'] ?: '' ;

        $response = Yii::$app->telegram->setWebhook(['url' => $url]);

        if (!$response->ok) {
            echo $response->description;
        } else {
            //VarDumper::dump($response);

            print_r(@json_decode(Yii::$app->telegram->getMe(), true));

            echo 'WebHook: ' . $url;
        }

        //Yii::$app->telegram->curl_call

        //  $jsonResponse = $this->curl_call("https://api.telegram.org/bot" . $this->botToken . "/setWebhook", $option);

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     *
     */
    public function actionClearUserSiteActivityLogs(): void
    {
        /* TODO:: remove this action */
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $count = UserSiteActivity::clearHistoryLogs();
        echo 'Removed: ' . $count;
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     *
     * @param int $limit
     * @throws InvalidConfigException
     *
     *
     * todo delete
     */
//    public function actionMigrateOldLeadLogsInGlobalLog($limit = 1000): void
    //  {
//      printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
//      $time_start = microtime(true);
//      \Yii::$app->db->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
//
//      $leadLog = LeadLog::find()->where(['>=', 'created', '2019-06-01 00:00:00'])->asArray();
//      $leadLogCount = $leadLog->count();
//
//      printf("\n --- Message: %s ---\n", $this->ansiFormat('Total rows: ' . $leadLogCount, Console::FG_GREEN));
//
//      $globalLog = Yii::createObject(GlobalLogInterface::class);
//
//      $c = 0;
//
//      $iter = (int)($leadLogCount / $limit);
//
//      $offset = 0;
//
//      for($i = 0; $i <= $iter; $i++) {
//          foreach (($leadLog->limit($limit)->offset($offset)->all()) as $log) {
//              if (($c % $limit) === 0) {
//                  if ($leadLogCount > 0) {
//                      $percent = round($c * 100 / $leadLogCount, 1);
//                  } else {
//                      $percent = 0;
//                  }
//
//                  $memory = Yii::$app->formatter->asShortSize(memory_get_usage(), 1);
//
//                  printf(" --- [%s] (%s) %s ---\n", $percent . '%', $memory, $this->ansiFormat( 'Current processed rows: ' . $c . ' of ' . $leadLogCount, Console::FG_PURPLE));
//              }
//
//              $message = json_decode($log['message'], true);
//
//              $modelPath = $this->getModelPath($message['model']);
//
//              if (!$modelPath) {
//                  echo 'Log will not be inserted, because the model ' . $message['model'] . ' was not found.' . PHP_EOL;
//                  continue;
//              }
//
//              $action = array_search($message['title'], GlobalLog::getActionTypeList(), false);
//
//              $this->removeDuplicatesFromOldNewAttributes($message['oldParams'], $message['newParams']);
//
//              $c++;
//              if (empty($message['oldParams']) && empty($message['newParams'])) {
//                  continue;
//              }
//
//              if ($message['model'] !== 'Lead') {
//                  if(!isset($message['newParams']['id'])) {
//                      if (preg_match('/\((.*?)\)/si', $message['model'], $output)) {
//                          $modelRowHashId = $output[1];
//
//                          $modelRowId = $this->findModelRowId($modelPath, $modelRowHashId, $log['lead_id']);
//
//                          if (!$modelRowId) {
//                              continue;
//                          }
//                      } else {
//                          continue;
//                      }
//                  } else {
//                      $modelRowId = $message['newParams']['id'];
//                  }
//              } else {
//                  $modelRowId = $log['lead_id'];
//              }
//
//              $formattedAttr = $this->globalLogFormatAttrService->formatAttr($modelPath, json_encode($message['oldParams']), json_encode($message['newParams']));
//
//              if (empty($formattedAttr)) {
//                  continue;
//              }
//
//              $globalLog->log(new LogDTO(
//                  $modelPath,
//                  $modelRowId,
//                  $log['employee_id'] ? 'app-frontend' : 'app-webapi',
//                  $log['employee_id'] ?? null,
//                  json_encode($message['oldParams']),
//                  json_encode($message['newParams']),
//                  $formattedAttr,
//                  $action ?: null,
//                  $log['created'] ?? null
//              ));
//          }
//          $offset += $limit;
//      }
//
//      $time_end = microtime(true);
//      $time = number_format(round($time_end - $time_start, 2), 2);
//      printf("\nExecute Time: %s, count Old Logs: " . $leadLogCount, $this->ansiFormat($time . ' s', Console::FG_RED));
//      printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    //  }

    public function actionTruncateGlobalLog(): void
    {
        try {
            Yii::$app->db->createCommand()->truncateTable('global_log')->execute();
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param int $level
     * @throws \Soundasleep\Html2TextException
     */
    public function actionCompressEmail(int $limit = 1000, int $offset = 0): void
    {
        $this->printInfo('Start', $this->action->id);
        $timeStart = microtime(true);
        $processed = 0;

        foreach ($this->findEmailBodyTextEmpty($limit, $offset) as $email) {
            $email->updateAttributes([
                'e_email_body_text' => TextConvertingHelper::htmlToText($email->e_email_body_html),
                'e_email_body_blob' => TextConvertingHelper::compress($email->e_email_body_html)
            ]);
            $processed++;
        }

        $resultInfo = 'Processed: ' . $processed .
            ', Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);

        Yii::info($resultInfo, 'info\DbController:actionCompressEmail');
        $this->printInfo($resultInfo, $this->action->id);
    }

    /**
     * @param int $limit
     * @param int $offset
     */
    public function actionCleanBodyHtml(int $limit = 1000, int $offset = 0): void
    {
        $this->printInfo('Start', $this->action->id, Console::FG_GREEN);
        $timeStart = microtime(true);
        $processed = $failed = 0;

        foreach ($this->findEmailBodyHtml($limit, $offset) as $email) {
            try {
                $email->updateAttributes([
                    'e_email_body_html' => null
                ]);
                $processed++;
            } catch (\Throwable $e) {
                $failed++;
                Yii::error(VarDumper::dumpAsString($e), self::class . ':' . $this->action->id . ':Clean failed');
            }
        }

        $resultInfo = 'Processed: ' . $processed . ' Failed: ' . $failed .
            ', Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);

        Yii::info($resultInfo, 'info\:' . self::class . ':' . $this->action->id);
        $this->printInfo($resultInfo, $this->action->id, Console::FG_GREEN);
    }

    /**
     * @param string $modelPath
     * @param int $leadId
     * @return int|null
     * @throws InvalidConfigException
     */
    public function findModelRowByLeadId(string $modelPath, int $leadId): ?int
    {
        echo 'findModelByLeadId' . PHP_EOL;
        /* @var ActiveRecord $model */
        $model = Yii::createObject($modelPath);

        $row = $model::find()
            ->select('id')
            ->andWhere([
                'lead_id' => $leadId
            ])->all();

        return count($row) !== 1 ? $row[0]->id : null;
    }

    /**
     * @param string $modelPath
     * @param string $modelRowHashId
     * @param int $leadId
     * @return int|null
     * @throws InvalidConfigException
     */
    private function findModelRowId(string $modelPath, string $modelRowHashId, int $leadId): ?int
    {
        /* @var ActiveRecord $model */
        $model = Yii::createObject($modelPath);

        $row = $model::find()
            ->select('id')
            ->andWhere([
            'uid' => $modelRowHashId,
            'lead_id' => $leadId
        ])->limit(1)->one();

        return $row->id ?? null;
    }

    /**
     * @param string $modelName
     * @return string|null
     */
    private function getModelPath(string $modelName): ?string
    {
        foreach (self::MODELS_PATH as $path) {
            try {
                return (new \ReflectionClass(explode(' ', $path . $modelName, 2)[0]))->getName();
            } catch (\ReflectionException $e) {
                echo 'ReflectionException: ' . $e->getMessage() . PHP_EOL;
            }
        }

        return null;
    }

    /**
     * @param array $oldAttributes
     * @param array $newAttributes
     */
    private function removeDuplicatesFromOldNewAttributes(array &$oldAttributes, array &$newAttributes): void
    {
        foreach ($newAttributes as $key => $value) {
            if ((array_key_exists($key, $newAttributes) && ($newAttributes[$key] == $oldAttributes[$key] || (empty($newAttributes[$key]) && empty($oldAttributes[$key]))))) {
                unset($newAttributes[$key], $oldAttributes[$key]);
            }
        }
    }

    /**
     * @param string $info
     * @param string $action
     * @param int $color
     */
    private function printInfo(string $info, string $action = '', $color = Console::FG_YELLOW)
    {
        printf("\n --- %s %s ---\n", $info, $this->ansiFormat(self::class . '/' . $action, $color));
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array|Email[]
     */
    private function findEmailBodyTextEmpty(int $limit, int $offset)
    {
        return Email::find()
            ->where(['e_email_body_text' => null])
            ->andWhere(['not', ['e_email_body_html' => null]])
            ->limit($limit)
            ->offset($offset)
            ->orderBy(['e_id' => SORT_ASC])
            ->all();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array|Email[]
     */
    private function findEmailBodyHtml(int $limit, int $offset)
    {
        return Email::find()
            ->where(['not', ['e_email_body_html' => null]])
            ->andWhere(['not', ['e_email_body_blob' => null]])
            ->limit($limit)
            ->offset($offset)
            ->orderBy(['e_id' => SORT_ASC])
            ->all();
    }


    /**
     * @param int $limit
     * @param int $offset
     */
    public function actionInitUserStatus(int $limit = 1000, int $offset = 0): void
    {
        $this->printInfo('Start', $this->action->id);
        $timeStart = microtime(true);

        $users = Employee::find()->limit($limit)->offset($offset)->all();
        $processed = 0;

        if ($users) {
            foreach ($users as $user) {
                $userStatus = $user->initUserStatus();
                if (!$userStatus) {
                    echo '- ' . $user->username . "\n";
                    VarDumper::dump($userStatus->attributes);
                    VarDumper::dump($userStatus->errors);
                } else {
                    echo '+ ' . ($processed + 1) . '. ' . $user->username . ', ps: ' . ($userStatus->us_call_phone_status ? 1 : 0) . "\n";
                }
                $processed++;
            }
        }

        $resultInfo = 'Processed: ' . $processed .
            ', Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);

        Yii::info($resultInfo, 'info\:' . self::class . ':' . $this->action->id);
        $this->printInfo($resultInfo, $this->action->id);
    }


    public function actionInitProjectCountry()
    {
        $this->printInfo('Start', $this->action->id);
        $timeStart = microtime(true);
        $data = [
            'AR' => [
                'facebook' => 'https://www.facebook.com/kayak.com.argentina',
                'instagram' => 'https://www.instagram.com/kayak_latam/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.ar',
                'privacy_policy' => 'https://www.kayak.com.ar/privacy',
                'terms_of_use' => 'https://www.kayak.com.ar/terms-of-use-book',
                'support_phone_number' => '+54 11 39899690',
                'verification_phone_number' => '+54 11 39899690'
            ],
            'AU' => [
                'facebook' => 'https://facebook.com/KAYAK.Australia',
                'instagram' => 'https://www.instagram.com/kayak_au/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.au',
                'privacy_policy' => 'https://www.kayak.com.au/privacy',
                'terms_of_use' => 'https://www.kayak.com.au/terms-of-use-book',
                'support_phone_number' => '+61 29 0378329',
                'verification_phone_number' => '+61 29 0378329'
            ],
            'AT' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.at.kayak.com',
                'privacy_policy' => 'https://www.at.kayak.com/privacy',
                'terms_of_use' => 'https://www.at.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'BE' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.be.kayak.com',
                'privacy_policy' => 'https://www.be.kayak.com/privacy',
                'terms_of_use' => 'https://www.be.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'BO' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.bo',
                'privacy_policy' => 'https://www.kayak.bo/privacy',
                'terms_of_use' => 'https://www.kayak.bo/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'BR' => [
                'facebook' => 'https://www.facebook.com/kayak.brazil',
                'instagram' => 'https://www.instagram.com/kayak_br/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.br',
                'privacy_policy' => 'https://www.kayak.com.br/privacy',
                'terms_of_use' => 'https://www.kayak.com.br/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'CA' => [
                'facebook' => 'https://www.facebook.com/kayak',
                'instagram' => 'https://www.instagram.com/kayak/',
                'twitter' => '',
                'homepage' => 'https://www.ca.kayak.com',
                'privacy_policy' => 'https://www.ca.kayak.com/privacy',
                'terms_of_use' => 'https://www.ca.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'CT' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.cat',
                'privacy_policy' => 'https://www.kayak.cat/privacy',
                'terms_of_use' => 'https://www.kayak.cat/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'CL' => [
                'facebook' => 'https://www.facebook.com/kayak.com.chile',
                'instagram' => 'https://www.instagram.com/kayak_latam/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.cl',
                'privacy_policy' => 'https://www.kayak.cl/privacy',
                'terms_of_use' => 'https://www.kayak.cl/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'CN' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.cn.kayak.com',
                'privacy_policy' => 'https://www.cn.kayak.com/privacy',
                'terms_of_use' => 'https://www.cn.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'CO' => [
                'facebook' => 'https://www.facebook.com/kayak.com.colombia',
                'instagram' => 'https://www.instagram.com/kayak_latam/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.co',
                'privacy_policy' => 'https://www.kayak.com.co/privacy',
                'terms_of_use' => 'https://www.kayak.com.co/terms-of-use-book',
                'support_phone_number' => '+57 1 3819260',
                'verification_phone_number' => '+ 57 1 3819260'
            ],
            'CR' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.co.cr',
                'privacy_policy' => 'https://www.kayak.co.cr/privacy',
                'terms_of_use' => 'https://www.kayak.co.cr/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'CZ' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.cz.kayak.com',
                'privacy_policy' => 'https://www.cz.kayak.com/privacy',
                'terms_of_use' => 'https://www.cz.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'DK' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.dk',
                'privacy_policy' => 'https://www.kayak.dk/privacy',
                'terms_of_use' => 'https://www.kayak.dk/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'DO' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.do',
                'privacy_policy' => 'https://www.kayak.com.do/privacy',
                'terms_of_use' => 'https://www.kayak.com.do/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'EC' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.ec',
                'privacy_policy' => 'https://www.kayak.com.ec/privacy',
                'terms_of_use' => 'https://www.kayak.com.ec/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'SV' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.sv',
                'privacy_policy' => 'https://www.kayak.com.sv/privacy',
                'terms_of_use' => 'https://www.kayak.com.sv/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'FI' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.fi.kayak.com',
                'privacy_policy' => 'https://www.fi.kayak.com/privacy',
                'terms_of_use' => 'https://www.fi.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'FR' => [
                'facebook' => '',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.fr',
                'privacy_policy' => 'https://www.kayak.fr/privacy',
                'terms_of_use' => 'https://www.kayak.fr/terms-of-use-book',
                'support_phone_number' => '+33 7 55539082',
                'verification_phone_number' => '+ 33 7 55539082'
            ],
            'DE' => [
                'facebook' => 'https://www.facebook.com/kayak.deutschland',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.de',
                'privacy_policy' => 'https://www.kayak.de/privacy',
                'terms_of_use' => 'https://www.kayak.de/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'GR' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.gr.kayak.com',
                'privacy_policy' => 'https://www.gr.kayak.com/privacy',
                'terms_of_use' => 'https://www.gr.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'GT' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.gt',
                'privacy_policy' => 'https://www.kayak.com.gt/privacy',
                'terms_of_use' => 'https://www.kayak.com.gt/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'HN' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.hn',
                'privacy_policy' => 'https://www.kayak.com.hn/privacy',
                'terms_of_use' => 'https://www.kayak.com.hn/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'HK' => [
                'facebook' => 'https://www.facebook.com/KAYAK.HongKong',
                'instagram' => 'https://www.instagram.com/kayak_hk/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.hk',
                'privacy_policy' => 'https://www.kayak.com.hk/privacy',
                'terms_of_use' => 'https://www.kayak.com.hk/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'IN' => [
                'facebook' => 'https://www.facebook.com/kayak.co.in',
                'instagram' => 'https://www.instagram.com/kayak_in/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.co.in',
                'privacy_policy' => 'https://www.kayak.co.in/privacy',
                'terms_of_use' => 'https://www.kayak.co.in/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'ID' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.co.id',
                'privacy_policy' => 'https://www.kayak.co.id/privacy',
                'terms_of_use' => 'https://www.kayak.co.id/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'IE' => [
                'facebook' => 'https://www.facebook.com/kayak.ireland',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.ie',
                'privacy_policy' => 'https://www.kayak.ie/privacy',
                'terms_of_use' => 'https://www.kayak.ie/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'IL' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.il.kayak.com',
                'privacy_policy' => 'https://www.il.kayak.com/privacy',
                'terms_of_use' => 'https://www.il.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'IT' => [
                'facebook' => 'https://facebook.com/kayak.italia',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.it',
                'privacy_policy' => 'https://www.kayak.it/privacy',
                'terms_of_use' => 'https://www.kayak.it/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'JP' => [
                'facebook' => 'https://www.facebook.com/kayak.co.jp',
                'instagram' => 'https://www.instagram.com/kayak.co.jp/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.co.jp',
                'privacy_policy' => 'https://www.kayak.co.jp/privacy',
                'terms_of_use' => 'https://www.kayak.co.jp/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'MY' => [
                'facebook' => 'https://www.facebook.com/KAYAK.com.my',
                'instagram' => 'https://www.instagram.com/kayak_my/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.my',
                'privacy_policy' => 'https://www.kayak.com.my/privacy',
                'terms_of_use' => 'https://www.kayak.com.my/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'MX' => [
                'facebook' => 'https://www.facebook.com/kayak.mex',
                'instagram' => 'https://www.instagram.com/kayak_latam/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.mx',
                'privacy_policy' => 'https://www.kayak.com.mx/privacy',
                'terms_of_use' => 'https://www.kayak.com.mx/terms-of-use-book',
                'support_phone_number' => '+52 664 3911436',
                'verification_phone_number' => '+ 52 664 3911436'
            ],
            'NL' => [
                'facebook' => 'https://www.facebook.com/kayak.netherlands',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.nl',
                'privacy_policy' => 'https://www.kayak.nl/privacy',
                'terms_of_use' => 'https://www.kayak.nl/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'NZ' => [
                'facebook' => 'https://www.facebook.com/KAYAK.NewZealand',
                'instagram' => 'https://www.instagram.com/kayak_nz/',
                'twitter' => '',
                'homepage' => 'https://www.nz.kayak.com',
                'privacy_policy' => 'https://www.nz.kayak.com/privacy',
                'terms_of_use' => 'https://www.nz.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'NI' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.ni',
                'privacy_policy' => 'https://www.kayak.com.ni/privacy',
                'terms_of_use' => 'https://www.kayak.com.ni/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'NO' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.no',
                'privacy_policy' => 'https://www.kayak.no/privacy',
                'terms_of_use' => 'https://www.kayak.no/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'PA' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.pa',
                'privacy_policy' => 'https://www.kayak.com.pa/privacy',
                'terms_of_use' => 'https://www.kayak.com.pa/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'PY' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.py',
                'privacy_policy' => 'https://www.kayak.com.py/privacy',
                'terms_of_use' => 'https://www.kayak.com.py/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'PE' => [
                'facebook' => 'https://www.facebook.com/KAYAKEnPeru',
                'instagram' => 'https://www.instagram.com/kayak_latam/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.pe',
                'privacy_policy' => 'https://www.kayak.com.pe/privacy',
                'terms_of_use' => 'https://www.kayak.com.pe/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'PH' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.ph',
                'privacy_policy' => 'https://www.kayak.com.ph/privacy',
                'terms_of_use' => 'https://www.kayak.com.ph/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'PL' => [
                'facebook' => 'https://www.facebook.com/KAYAK.Poland',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.pl',
                'privacy_policy' => 'https://www.kayak.pl/privacy',
                'terms_of_use' => 'https://www.kayak.pl/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'PT' => [
                'facebook' => 'https://www.facebook.com/kayak.pt',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.pt',
                'privacy_policy' => 'https://www.kayak.pt/privacy',
                'terms_of_use' => 'https://www.kayak.pt/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'PR' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.pr',
                'privacy_policy' => 'https://www.kayak.com.pr/privacy',
                'terms_of_use' => 'https://www.kayak.com.pr/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'RO' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.ro.kayak.com',
                'privacy_policy' => 'https://www.ro.kayak.com/privacy',
                'terms_of_use' => 'https://www.ro.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'RU' => [
                'facebook' => 'https://www.facebook.com/kayak.russia',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.ru',
                'privacy_policy' => 'https://www.kayak.ru/privacy',
                'terms_of_use' => 'https://www.kayak.ru/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'SA' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.ar.kayak.sa',
                'privacy_policy' => 'https://www.ar.kayak.sa/privacy',
                'terms_of_use' => 'https://www.ar.kayak.sa/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'SG' => [
                'facebook' => 'https://www.facebook.com/KAYAK.Singapore',
                'instagram' => 'https://www.instagram.com/kayak_sg/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.sg',
                'privacy_policy' => 'https://www.kayak.sg/privacy',
                'terms_of_use' => 'https://www.kayak.sg/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'ZA' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.za.kayak.com',
                'privacy_policy' => 'https://www.za.kayak.com/privacy',
                'terms_of_use' => 'https://www.za.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'KR' => [
                'facebook' => 'https://facebook.com/KAYAK.co.kr',
                'instagram' => 'https://www.instagram.com/kayak_kr/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.co.kr',
                'privacy_policy' => 'https://www.kayak.co.kr/privacy',
                'terms_of_use' => 'https://www.kayak.co.kr/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'ES' => [
                'facebook' => 'https://www.facebook.com/kayak.espana',
                'instagram' => 'https://www.instagram.com/kayak_espana/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.es',
                'privacy_policy' => 'https://www.kayak.es/privacy',
                'terms_of_use' => 'https://www.kayak.es/terms-of-use-book',
                'support_phone_number' => '+34 800 880867',
                'verification_phone_number' => '+34 800 880867'
            ],
            'SE' => [
                'facebook' => 'https://www.facebook.com/kayak.sverige',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.se',
                'privacy_policy' => 'https://www.kayak.se/privacy',
                'terms_of_use' => 'https://www.kayak.se/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'CH' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.ch',
                'privacy_policy' => 'https://www.kayak.ch/privacy',
                'terms_of_use' => 'https://www.kayak.ch/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'TW' => [
                'facebook' => 'https://www.facebook.com/KAYAK.com.Taiwan',
                'instagram' => 'https://www.instagram.com/kayak_tw/',
                'twitter' => '',
                'homepage' => 'https://www.tw.kayak.com',
                'privacy_policy' => 'https://www.tw.kayak.com/privacy',
                'terms_of_use' => 'https://www.tw.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'TH' => [
                'facebook' => 'https://www.facebook.com/KAYAK.co.th',
                'instagram' => 'https://www.instagram.com/kayak_th/',
                'twitter' => '',
                'homepage' => 'https://www.kayak.co.th',
                'privacy_policy' => 'https://www.kayak.co.th/privacy',
                'terms_of_use' => 'https://www.kayak.co.th/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'TR' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.tr',
                'privacy_policy' => 'https://www.kayak.com.tr/privacy',
                'terms_of_use' => 'https://www.kayak.com.tr/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'UA' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.ua.kayak.com',
                'privacy_policy' => 'https://www.ua.kayak.com/privacy',
                'terms_of_use' => 'https://www.ua.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'AE' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.ae',
                'privacy_policy' => 'https://www.kayak.ae/privacy',
                'terms_of_use' => 'https://www.kayak.ae/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'GB' => [
                'facebook' => '',
                'instagram' => 'https://www.instagram.com/kayak_europe/',
                'twitter' => 'https://twitter.com/KAYAK_UK',
                'homepage' => 'https://www.kayak.co.uk',
                'privacy_policy' => 'https://www.kayak.co.uk/privacy',
                'terms_of_use' => 'https://www.kayak.co.uk/terms-of-use-book',
                'support_phone_number' => '+44 8081967575',
                'verification_phone_number' => '+44 8081967575'
            ],
            'US' => [
                'facebook' => 'https://www.facebook.com/kayak',
                'instagram' => 'https://www.instagram.com/kayak/',
                'twitter' => 'https://twitter.com/KAYAK',
                'homepage' => 'https://www.kayak.com',
                'privacy_policy' => 'https://www.kayak.com/privacy',
                'terms_of_use' => 'https://www.kayak.com/terms-of-use-book',
                'support_phone_number' => '+1 (855) 920-9942',
                'verification_phone_number' => '+1 (844) 962-3974'
            ],
            'UY' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.com.uy',
                'privacy_policy' => 'https://www.kayak.com.uy/privacy',
                'terms_of_use' => 'https://www.kayak.com.uy/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'VE' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.kayak.co.ve',
                'privacy_policy' => 'https://www.kayak.co.ve/privacy',
                'terms_of_use' => 'https://www.kayak.co.ve/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
            'VN' => [
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'homepage' => 'https://www.vn.kayak.com',
                'privacy_policy' => 'https://www.vn.kayak.com/privacy',
                'terms_of_use' => 'https://www.vn.kayak.com/terms-of-use-book',
                'support_phone_number' => '',
                'verification_phone_number' => ''
            ],
        ];

        $project = Project::find()->where(['project_key' => 'kayak'])->one();

        $processed = 0;

        if ($project && $data) {
            foreach ($data as $marketCountry => $mp) {
                echo ' - ' . $marketCountry . PHP_EOL;

                $params = [];
                $params['homeUrl']              = $mp['homepage'];
                $params['privacyPolicy']        = $mp['privacy_policy'];
                $params['termsOfUse']           = $mp['terms_of_use'];

                $params['social']['facebook']   = $mp['facebook'];
                $params['social']['instagram']  = $mp['instagram'];
                $params['social']['twitter']    = $mp['twitter'];

                $params['phoneNumbers']['support']          = $mp['support_phone_number'];
                $params['phoneNumbers']['verification']     = $mp['verification_phone_number'];

                $params['formatter']['time']        = 'HH:mm';
                $params['formatter']['date']        = '';

                $json = json_encode($params);

                $locale = new ProjectLocale();
                $locale->pl_project_id = $project->id;
                $locale->pl_params = $json;
                $locale->pl_market_country = $marketCountry;
                $locale->pl_enabled = true;
                $locale->save();

                $processed++;
                echo $json . PHP_EOL;
            }
        }

        $resultInfo = 'Processed: ' . $processed .
            ', Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);

        $this->printInfo($resultInfo, $this->action->id);
    }

    public function actionInitProjectLocales()
    {
        $this->printInfo('Start', $this->action->id);
        $timeStart = microtime(true);

        $projects = Project::find()->all();

        $processed = 0;

        if ($projects) {
            foreach ($projects as $project) {
                echo ' - ' . $project->project_key . PHP_EOL;

                $params = [];
                $params['homeUrl']              = '';
                $params['privacyPolicy']        = '';
                $params['termsOfUse']           = '';

                $params['social']['facebook']   = '';
                $params['social']['instagram']  = '';
                $params['social']['twitter']    = '';

                $params['phoneNumbers']['support']          = '';
                $params['phoneNumbers']['verification']     = '';

                $params['formatter']['time']        = 'HH:mm';
                $params['formatter']['date']        = '';

                $json = json_encode($params);
                echo $json . PHP_EOL;

                $locale = new ProjectLocale();
                $locale->pl_project_id = $project->id;
                $locale->pl_language_id = 'en-US';
                $locale->pl_params = $json;
                //$locale->pl_market_country = $marketCountry;
                $locale->pl_enabled = true;
                $locale->pl_default = true;
                $locale->save();

                $processed++;
            }
        }

        $resultInfo = 'Processed: ' . $processed .
            ', Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);

        $this->printInfo($resultInfo, $this->action->id);
    }

    public function actionInitView()
    {
        $this->printInfo('Start', $this->action->id);
        $timeStart = microtime(true);

        $db = Yii::$app->getDb();
        /** @var DbDataSensitive[] $dateSensitives */
        $dbDataSensitives = DbDataSensitive::find()->all();

        foreach ($dbDataSensitives as $dbDataSensitive) {
            $data = JsonHelper::decode($dbDataSensitive->dda_source);
            foreach ($data as $tableName => $columns) {
                try {
                    $this->dbDataSensitiveService->createView($db, $dbDataSensitive, $tableName, $columns);
                    echo Console::renderColoredString('%g --- Created : %w[' . $tableName . '_' . $dbDataSensitive->dda_key . ']%n'), PHP_EOL;
                } catch (\RuntimeException | \DomainException $throwable) {
                    echo Console::renderColoredString('%y --- Warning : %c[' . $tableName . ']: ' . $throwable->getMessage() . ' %n'), PHP_EOL;
                } catch (\Throwable $throwable) {
                    $message = AppHelper::throwableLog($throwable);
                    $message['tableName'] = $tableName;
                    Yii::error($message, 'DbController:actionInitView:Throwable');
                    echo Console::renderColoredString('%r --- Error : %p[' . $tableName . ']: ' . $throwable->getMessage() . ' %n'), PHP_EOL;
                }
            }
        }
        $resultInfo = 'Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);
        $this->printInfo($resultInfo, $this->action->id);
    }

    public function actionDropView(string $viewName)
    {
        $this->printInfo('Start', $this->action->id);
        $timeStart = microtime(true);

        if (empty($viewName) || !($dbDataSensitiveView = DbDataSensitiveView::findOne(['ddv_view_name' => $viewName]))) {
            echo Console::renderColoredString('%r --- Error : %p "viewName" is required %n'), PHP_EOL;
            exit();
        }

        try {
            $this->dbDataSensitiveService->dropViewByDbDataSensitiveView($dbDataSensitiveView);
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['viewName'] = $viewName;
            Yii::error($message, 'DbController:actionDropView:Throwable');
            echo Console::renderColoredString('%r --- Error : %p[' . $dbDataSensitiveView->ddv_table_name . ']: ' . $throwable->getMessage() . ' %n'), PHP_EOL;
        }

        $resultInfo = 'Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);
        $this->printInfo($resultInfo, $this->action->id);
    }

    /**
     * Regenerate views from db_data_sensitive (where dda_key = view)
     */
    public function actionRegenerateDefaultSensitiveViews()
    {
        try {
            $this->printInfo('Start....', $this->action->id, Console::BG_GREEN);

            $this->printInfo('1/4 Searching default db_data_sensetive', $this->action->id, Console::BG_GREEN);
            /** @var DbDataSensitive $sensitiveEntity */
            $sensitiveEntity = DbDataSensitive::find()
                ->andWhere(['dda_key' => 'view'])
                ->one();

            if (empty($sensitiveEntity)) {
                throw new NotFoundException('Default view not found');
            }

            $this->printInfo('2/4 Droping default views', $this->action->id, Console::BG_GREEN);
            $this->dbDataSensitiveService->dropViews($sensitiveEntity);

            $this->printInfo('3/4 Update source of default db_data_sensetive', $this->action->id, Console::BG_GREEN);
            $sensitiveEntity->load([
                'dda_source' => DbDataSensitiveDictionary::SOURCE
            ]);
            $this->dbDataSensitiveRepository->save($sensitiveEntity);

            $this->printInfo('4/4 Regeneration default views', $this->action->id, Console::BG_GREEN);
            $this->dbDataSensitiveService->createViews($sensitiveEntity);

            $this->printInfo('Done!', $this->action->id, Console::BG_GREEN);
        } catch (\Throwable $e) {
            Yii::error($e, 'DbController:actionRegenerateDefaultViews:Throwable');
            $this->printInfo($e->getMessage(), $this->action->id, Console::BG_RED);
        }
    }
}
