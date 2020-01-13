<?php
namespace console\controllers;

use common\models\Airline;
use common\models\ClientPhone;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\GlobalLog;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadLog;
use common\models\LeadQcall;
use common\models\Project;
use common\models\ProjectWeight;
use common\models\Quote;
use common\models\UserProjectParams;
use frontend\models\UserSiteActivity;
use sales\entities\cases\Cases;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;
use sales\services\lead\qcall\CalculateDateService;
use sales\services\log\GlobalLogFormatAttrService;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class DbController
 * @package console\controllers
 *
 * @property GlobalLogFormatAttrService $globalLogFormatAttrService
 */
class DbController extends Controller
{

	private const MODELS_PATH = [
		'\\common\\models\\',
		'\\frontend\\models\\'
	];
	/**
	 * @var GlobalLogFormatAttrService
	 */
	private $globalLogFormatAttrService;

    /**
     * DbController constructor.
     * @param $id
     * @param $module
     * @param GlobalLogFormatAttrService $globalLogFormatAttrService
     * @param array $config
     */
    public function __construct($id, $module, GlobalLogFormatAttrService $globalLogFormatAttrService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->globalLogFormatAttrService = $globalLogFormatAttrService;
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

        Yii::$app->db->createCommand()->batchInsert('{{%project_weight}}', ['pw_project_id'],$projects)->execute();
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
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables
        $tables = $db->createCommand('SELECT table_name FROM information_schema.tables WHERE table_schema=:schema AND table_type = "BASE TABLE"', [
            ':schema' => $schema
        ])->queryAll();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();

        //VarDumper::dump($tables); exit;

        // Alter the encoding of each table
        foreach ($tables as $id => $table) {
            if(isset($table['table_name'])) {
                $tableName = $table['table_name'];
                $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci")->execute();
                echo $id." - tbl: " . $tableName . "\r\n";
            }

            if(isset($table['TABLE_NAME'])) {
                $tableName = $table['TABLE_NAME'];
                $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci")->execute();
                echo $id." - tbl: " . $tableName . "\r\n";
            }
        }
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

        Airline::syncCabinClasses();

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

        if($logs) {
            foreach ($logs as $nr => $log) {
                LeadFlow::updateAll(['lf_from_status_id' => $log['from_status_id'], 'lf_end_dt' => $log['end_dt'], 'lf_time_duration' => $log['time_duration']], ['id' => $log['id']]);
                echo $nr.' - id: '.$log['id']."\n";
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

        $quotes = Quote::find()->leftJoin('quote_trip','quote_trip.qt_quote_id = quotes.id')->where(['quote_trip.qt_id' => null])->all();
        printf("\n Quotes to update: %d \n", count($quotes));
        if(count($quotes)){
            $cntUpdated = 0;
            foreach ($quotes as $quote){
                if($quote->createQuoteTrips()){
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

        if(!$response->ok) {
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
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $count = UserSiteActivity::clearHistoryLogs();
        echo 'Removed: ' . $count;
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

	/**
	 *
	 * @param int $limit
	 * @throws InvalidConfigException
	 */
    public function actionMigrateOldLeadLogsInGlobalLog($limit = 1000): void
	{
		printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
		$time_start = microtime(true);
		\Yii::$app->db->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

		$leadLog = LeadLog::find()->where(['>=', 'created', '2019-06-01 00:00:00'])->asArray();
		$leadLogCount = $leadLog->count();

		printf("\n --- Message: %s ---\n", $this->ansiFormat('Total rows: ' . $leadLogCount, Console::FG_GREEN));

		$globalLog = Yii::createObject(GlobalLogInterface::class);

		$c = 0;

		$iter = (int)($leadLogCount / $limit);

		$offset = 0;

		for($i = 0; $i <= $iter; $i++) {
			foreach (($leadLog->limit($limit)->offset($offset)->all()) as $log) {
				if (($c % $limit) === 0) {
					if ($leadLogCount > 0) {
						$percent = round($c * 100 / $leadLogCount, 1);
					} else {
						$percent = 0;
					}

					$memory = Yii::$app->formatter->asShortSize(memory_get_usage(), 1);

					printf(" --- [%s] (%s) %s ---\n", $percent . '%', $memory, $this->ansiFormat( 'Current processed rows: ' . $c . ' of ' . $leadLogCount, Console::FG_PURPLE));
				}

				$message = json_decode($log['message'], true);

				$modelPath = $this->getModelPath($message['model']);

				if (!$modelPath) {
					echo 'Log will not be inserted, because the model ' . $message['model'] . ' was not found.' . PHP_EOL;
					continue;
				}

				$action = array_search($message['title'], GlobalLog::getActionTypeList(), false);

				$this->removeDuplicatesFromOldNewAttributes($message['oldParams'], $message['newParams']);

				$c++;
				if (empty($message['oldParams']) && empty($message['newParams'])) {
					continue;
				}

				if ($message['model'] !== 'Lead') {
					if(!isset($message['newParams']['id'])) {
						if (preg_match('/\((.*?)\)/si', $message['model'], $output)) {
							$modelRowHashId = $output[1];

							$modelRowId = $this->findModelRowId($modelPath, $modelRowHashId, $log['lead_id']);

							if (!$modelRowId) {
								continue;
							}
						} else {
							continue;
						}
					} else {
						$modelRowId = $message['newParams']['id'];
					}
				} else {
					$modelRowId = $log['lead_id'];
				}

				$formattedAttr = $this->globalLogFormatAttrService->formatAttr($modelPath, json_encode($message['oldParams']), json_encode($message['newParams']));

				if (empty($formattedAttr)) {
					continue;
				}

				$globalLog->log(new LogDTO(
					$modelPath,
					$modelRowId,
					$log['employee_id'] ? 'app-frontend' : 'app-webapi',
					$log['employee_id'] ?? null,
					json_encode($message['oldParams']),
					json_encode($message['newParams']),
					$formattedAttr,
					$action ?: null,
					$log['created'] ?? null
				));
			}
			$offset += $limit;
		}

		$time_end = microtime(true);
		$time = number_format(round($time_end - $time_start, 2), 2);
		printf("\nExecute Time: %s, count Old Logs: " . $leadLogCount, $this->ansiFormat($time . ' s', Console::FG_RED));
		printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
	}

	public function actionTruncateGlobalLog(): void
	{
		try {
			Yii::$app->db->createCommand()->truncateTable('global_log')->execute();
		} catch (Exception $e) {
			echo $e->getMessage() . PHP_EOL;
		}
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
}