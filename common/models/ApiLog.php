<?php

namespace common\models;

use common\models\query\ApiLogQuery;
use DateTime;
use frontend\helpers\JsonHelper;
use webapi\src\logger\EndDTO;
use webapi\src\logger\StartDTO;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "api_log".
 *
 * @property int $al_id
 * @property string $al_request_data
 * @property string $al_request_dt
 * @property string $al_response_data
 * @property string $al_response_dt
 * @property string $al_ip_address
 * @property integer $al_user_id
 * @property string $al_action
 * @property float $al_execution_time
 * @property integer $al_memory_usage
 * @property float $al_db_execution_time
 * @property integer $al_db_query_count
 * @property string $al_created_dt
 *
 * @property $start_microtime
 * @property $end_microtime
 * @property $start_memory_usage
 * @property $end_memory_usage
 *
 * @property ApiUser[] $apiUser
 */
class ApiLog extends \yii\db\ActiveRecord
{
    public $start_microtime = 0;
    public $end_microtime = 0;

    public $start_memory_usage = 0;
    public $end_memory_usage = 0;

    private $attempts = 0;

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        return \Yii::$app->get('db_postgres');
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['al_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public static function start(StartDTO $dto): self
    {
        $log = new static();
        $log->al_request_data = $dto->data;
        $log->al_action = $dto->action;
        $log->al_user_id = $dto->userId;
        $log->al_ip_address = $dto->ip;
        $log->start_microtime = $dto->startTime;
        $log->start_memory_usage = $dto->startMemory;
        $log->al_request_dt = date('Y-m-d H:i:s');
        return $log;
    }

    public function end(EndDTO $dto): void
    {
        $this->al_response_data = $dto->result;
        $this->al_response_dt = date('Y-m-d H:i:s');
        $this->end_microtime = $dto->endTime;
        $this->end_memory_usage = $dto->endMemory;
        $this->profiling($dto->profiling);
        $this->calculateExecutionTime();
        $this->calculateMemoryUsage();
    }

    public function profiling(array $profiling): void
    {
        if ($profiling) {
            if (isset($profiling[0])) {
                $this->al_db_query_count = (int)$profiling[0];
            }
            if (isset($profiling[1])) {
                $this->al_db_execution_time = round($profiling[1], 3);
            }
        }
    }

    public function calculateExecutionTime(): void
    {
        if ($this->end_microtime && $this->start_microtime) {
            if (($time = round($this->end_microtime - $this->start_microtime, 3)) > 999) {
                $time = 999;
            }
            $this->al_execution_time = $time;
        } else {
            $this->al_execution_time = 0;
        }
    }

    public function calculateMemoryUsage(): void
    {
        if ($this->end_memory_usage && $this->start_memory_usage) {
            $this->al_memory_usage = $this->end_memory_usage - $this->start_memory_usage;
        } else {
            $this->al_memory_usage = 0;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['al_request_data', 'al_request_dt'], 'required'],
            [['al_request_data', 'al_response_data', 'al_created_dt'], 'string'],
            [['al_user_id', 'al_memory_usage', 'al_db_query_count'], 'integer'],
            [['al_execution_time', 'al_db_execution_time'], 'double'],
            [['al_request_dt', 'al_response_dt'], 'safe'],
            [['al_ip_address'], 'string', 'max' => 40],
            [['al_action'], 'string', 'max' => 255],
        ];
    }

    public function save($runValidation = true, $attributeNames = null): bool
    {
        try {
            $result = parent::save($runValidation, $attributeNames);
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), "no partition of relation")) {
                $dates = self::partitionDatesFrom(date_create_from_format('Y-m-d H:i:s', $this->al_created_dt));
                self::createMonthlyPartition($dates[0], $dates[1]);
                if ($this->attempts > 0) {
                    throw new \RuntimeException("unable to create api_log partition");
                }
                ++$this->attempts;
                return $this->save($runValidation, $attributeNames);
            }
            return false;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'al_id' => 'ID',
            'al_request_data' => 'Request Data',
            'al_request_dt' => 'Request Dt',
            'al_response_data' => 'Response Data',
            'al_response_dt' => 'Response Dt',
            'al_ip_address' => 'Ip Address',
            'al_user_id' => 'User',
            'al_action' => 'Action',
            'al_execution_time' => 'Execution time',
            'al_memory_usage' => 'Memory usage',
            'al_db_query_count' => 'Query count',
            'al_db_execution_time' => 'DB execution time',
            'al_created_dt' => 'Created DT'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApiUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ApiUser::class, ['au_id' => 'al_user_id']);
    }

    /**
     * @inheritdoc
     * @return ApiLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ApiLogQuery(static::class);
    }

    /**
     * @param array $responseData
     * @return array
     */
    public function endApiLog(array $responseData = []): array
    {
        $this->al_response_data = @json_encode($responseData);
        $this->al_response_dt = date('Y-m-d H:i:s');

        $this->end_microtime = microtime(true);
        $this->end_memory_usage = memory_get_usage();

        if ($this->start_microtime) {
            $time = round($this->end_microtime - $this->start_microtime, 3);
        } else {
            $time = 0;
        }

        if ($time > 999) {
            $time = 999;
        }

        if ($this->start_memory_usage) {
            $memory_usage = $this->end_memory_usage - $this->start_memory_usage;
        } else {
            $memory_usage = 0;
        }


        //VarDumper::dump($time);exit;

        $this->al_execution_time = $time;
        $this->al_memory_usage = $memory_usage;

        $profiling = Yii::getLogger()->getDbProfiling();

        if ($profiling) {
            if (isset($profiling[0])) {
                $this->al_db_query_count = (int) $profiling[0];
            }

            if (isset($profiling[1])) {
                $this->al_db_execution_time = round($profiling[1], 3);
            }
        }

        $this->logToElk();

        if ($this->save()) {
            $responseData['action']             = $this->al_action;
            $responseData['response_id']        = $this->al_id;
            $responseData['request_dt']         = $this->al_request_dt;
            $responseData['response_dt']        = $this->al_response_dt;
            $responseData['execution_time']     = $this->al_execution_time;
            $responseData['memory_usage']       = $this->al_memory_usage;
        } else {
            Yii::error(print_r($this->errors, true), 'ApiLog:endApiLog:save');
        }

        return $responseData;
    }

    /**
     * @param bool $countGroup
     * @return array
     */
    public static function getActionFilter(bool $countGroup = false): array
    {
        $arr = [];

        if ($countGroup) {
            $data = self::find()->select(["COUNT(*) AS cnt", "al_action"])
                ->where('al_action IS NOT NULL')
                ->groupBy(["al_action"])
                ->orderBy('cnt DESC')
                ->cache(60)
                ->asArray()->all();

            if ($data) {
                foreach ($data as $v) {
                    $arr[$v['al_action']] = $v['al_action'] . ' - [' . $v['cnt'] . ']';
                }
            }
        } else {
            $data = self::find()->select("DISTINCT(al_action) AS al_action")
                ->cache(60)
                ->orderBy('al_action')
                ->asArray()->all();

            if ($data) {
                foreach ($data as $v) {
                    $arr[$v['al_action']] = $v['al_action'];
                }
            }
        }

        return $arr;
    }

    /**
     * @return array
     */
    public static function getActionFilterByCnt(): array
    {
        $arr = [];
        $data = self::find()->select(["COUNT(*) AS cnt", "al_action"])
            ->where('al_action IS NOT NULL')
            ->groupBy(["al_action"])
            ->orderBy('cnt DESC')
            ->cache(60)
            ->asArray()->all();

        if ($data) {
            foreach ($data as $v) {
                $arr[] = [
                    'hash' => md5($v['al_action']),
                    'name' => $v['al_action'],
                    'cnt' => $v['cnt']
                ];
            }
        }

        return $arr;
    }


    public static function getActionsList()
    {
        return self::find()->select('al_action')->where('al_action IS NOT NULL')->distinct()->asArray()->all();
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey(): array
    {
        return ["al_id"];
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Calculate from and to dates from a given date.
     * Given date -> from = start of the month, to = next month start date
     *
     * @param DateTime $date partition start date
     * @return array DateTime table_name created table
     * @throws \RuntimeException any errors occurred during execution
     */
    public static function partitionDatesFrom(DateTime $date): array
    {
        $monthBegin = date('Y-m-d', strtotime(date_format($date, 'Y-m-1')));
        if (!$monthBegin) {
            throw new \RuntimeException("invalid partition start date");
        }

        $partitionStartDate = date_create_from_format('Y-m-d', $monthBegin);
        $partitionEndDate = date_create_from_format('Y-m-d', $monthBegin);

        date_add($partitionEndDate, date_interval_create_from_date_string("1 month"));

        return [$partitionStartDate, $partitionEndDate];
    }

    /**
     * Create a partition table with indicated from and to date
     *
     * @param DateTime $partFromDateTime partition start date
     * @param DateTime $partToDateTime partition end date
     * @return string table_name created table
     * @throws \yii\db\Exception
     */
    public static function createMonthlyPartition(DateTime $partFromDateTime, DateTime $partToDateTime): string
    {
        $db = self::getDb();
        $partTableName = self::tableName() . "_" . date_format($partFromDateTime, "Y_m");
        $cmd = $db->createCommand("create table " . $partTableName . " PARTITION OF " . self::tableName() .
            " FOR VALUES FROM ('" . date_format($partFromDateTime, "Y-m-d") . "') TO ('" . date_format($partToDateTime, "Y-m-d") . "')");
        $cmd->execute();
        return $partTableName;
    }

    public function logToElk(string $type = 'apilog', string $prefix = 'elk'): void
    {
        $logData['type']                = $type;
        $reqData['request_data']        = JsonHelper::decode($this->al_request_data);
        $reqData['request_dt']          = $this->al_request_dt;
        $reqData['ip_address']          = $this->al_ip_address;
        $reqData['user_id']             = $this->al_user_id;
        $reqData['action']              = $this->al_action;

        $logData['request']             = $reqData;

        $respData['response_data']      = JsonHelper::decode($this->al_response_data);
        $respData['response_id']        = $this->al_id;
        $respData['response_dt']        = $this->al_response_dt;

        $logData['response']            = $respData;

        $logData['execution_time']      = $this->al_execution_time;
        $logData['memory_usage']        = $this->al_memory_usage;
        $logData['db_execution_time']   = $this->al_db_execution_time;
        $logData['db_query_count']      = $this->al_db_query_count;

        Yii::info($logData, $prefix . '/' . $this->al_action);
        unset($logData, $reqData, $respData);
    }
}
