<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\VarDumper;

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
 *
 * @property ApiUser[] $apiUser
 */
class ApiLog extends \yii\db\ActiveRecord
{

    public $start_microtime = 0;
    public $end_microtime = 0;

    public $start_memory_usage = 0;
    public $end_memory_usage = 0;

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
            [['al_request_data', 'al_response_data'], 'string'],
            [['al_user_id', 'al_memory_usage', 'al_db_query_count'], 'integer'],
            [['al_execution_time', 'al_db_execution_time'], 'double'],
            [['al_request_dt', 'al_response_dt'], 'safe'],
            [['al_ip_address'], 'string', 'max' => 40],
            [['al_action'], 'string', 'max' => 255],
        ];
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
        return new ApiLogQuery(get_called_class());
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

        if($this->start_microtime) {
            $time = round($this->end_microtime - $this->start_microtime, 3);
        } else {
            $time = 0;
        }

        if($time > 999) {
            $time = 999;
        }

        if($this->start_memory_usage) {
            $memory_usage = $this->end_memory_usage - $this->start_memory_usage;
        } else {
            $memory_usage = 0;
        }


        //VarDumper::dump($time);exit;

        $this->al_execution_time = $time;
        $this->al_memory_usage = $memory_usage;

        $profiling = Yii::getLogger()->getDbProfiling();

        if($profiling) {
            if (isset($profiling[0])) {
                $this->al_db_query_count = (int) $profiling[0];
            }

            if (isset($profiling[1])) {
                $this->al_db_execution_time = round($profiling[1], 3);
            }
        }


        //VarDumper::dump($profiling);exit;

        if($this->save()) {

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
     * @return array
     */
    public static function getActionFilter(): array
    {
        $arr = [];
        $data = self::find()->select(['COUNT(*) AS cnt', 'al_action'])
            ->where('al_action IS NOT NULL')
            //->andWhere("job_start_dt >= NOW() - interval '24 hour'")
            ->groupBy(['al_action'])
            ->orderBy('cnt DESC')->asArray()->all();

        if($data) {
            foreach ($data as $v) {
                $arr[$v['al_action']] = $v['al_action'] . ' - [' . $v['cnt'] . ']';
            }
        }

        return $arr;
    }

    /**
     * @param string $fromDate
     * @param string $todate
     * @param string $range
     * @param string $apiUserId
     * @return array
     */
    public static function getApiLogStats(string $fromDate, string $todate, string $range, string $apiUserId) : array
    {
        if ($range == 'H'){
            $queryDateFormat = '%H:00';
        } elseif ($range == 'D'){
            $queryDateFormat = '%Y-%m-%d';
        } elseif ($range == 'M'){
            $queryDateFormat = '%Y-%m';
        } elseif ($range == 'HD'){
            $queryDateFormat = '%Y-%m-%d %H:00';
        }

        $cVoiceQuery = new Query();
        $cVoiceQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS cVoice, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS cAvgTimeV", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS cMemV"]);
        $cVoiceQuery->from('api_log');
        $cVoiceQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $cVoiceQuery->andWhere(['=', 'al_action', 'v1/communication/voice']);
        if($apiUserId != ''){
            $cVoiceQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $cVoiceQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $communicationVoice = $cVoiceQuery->all();

        $cSmsQuery = new Query();
        $cSmsQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS cSms, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS cAvgTimeS", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS cMemS"]);
        $cSmsQuery->from('api_log');
        $cSmsQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $cSmsQuery->andWhere(['=', 'al_action', 'v1/communication/sms']);
        if($apiUserId != ''){
            $cSmsQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $cSmsQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $communicationSms = $cSmsQuery->all();

        $cEmailQuery = new Query();
        $cEmailQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS cEmail, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS cAvgTimeE", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS cMemE"]);
        $cEmailQuery->from('api_log');
        $cEmailQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $cEmailQuery->andWhere(['=', 'al_action', 'v1/communication/email']);
        if($apiUserId != ''){
            $cEmailQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $cEmailQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $communicationEmail = $cEmailQuery->all();

        $lCreateQuery = new Query();
        $lCreateQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS lCreate, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS lAvgTimeC", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS lMemC"]);
        $lCreateQuery->from('api_log');
        $lCreateQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $lCreateQuery->andWhere(['=', 'al_action', 'v1/lead/create']);
        if($apiUserId != ''){
            $lCreateQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $lCreateQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $leadCreate = $lCreateQuery->all();

        $lSoldUpdateQuery = new Query();
        $lSoldUpdateQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS leadSU, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS lAvgTimeSU", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS lMemSU"]);
        $lSoldUpdateQuery->from('api_log');
        $lSoldUpdateQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $lSoldUpdateQuery->andWhere(['=', 'al_action', 'v1/lead/sold-update']);
        if($apiUserId != ''){
            $lSoldUpdateQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $lSoldUpdateQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $leadSoldUpdate = $lSoldUpdateQuery->all();

        $qCreateQuery = new Query();
        $qCreateQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS qCreate, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS qAvgTimeC", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS qMemC"]);
        $qCreateQuery->from('api_log');
        $qCreateQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $qCreateQuery->andWhere(['=', 'al_action', 'v1/quote/create']);
        if($apiUserId != ''){
            $qCreateQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $qCreateQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $quoteCreate = $qCreateQuery->all();

        $qUpdateQuery = new Query();
        $qUpdateQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS qUpdate, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS qAvgTimeU", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS qMemU"]);
        $qUpdateQuery->from('api_log');
        $qUpdateQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $qUpdateQuery->andWhere(['=', 'al_action', 'v1/quote/update']);
        if($apiUserId != ''){
            $qUpdateQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $qUpdateQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $quoteUpdate = $qUpdateQuery->all();

        $qGetInfoQuery = new Query();
        $qGetInfoQuery->select(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ') AS timeLine, COUNT(*) AS qInfo, SUM(CASE WHEN al_execution_time >=0 THEN al_execution_time ELSE 0 END) AS qAvgTimeI", "SUM(CASE WHEN al_memory_usage >=0 THEN al_memory_usage ELSE 0 END) AS qMemI"]);
        $qGetInfoQuery->from('api_log');
        $qGetInfoQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $qGetInfoQuery->andWhere(['=', 'al_action', 'v2/quote/get-info']);
        if($apiUserId != ''){
            $qGetInfoQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        $qGetInfoQuery->groupBy(["DATE_FORMAT( al_request_dt, ' $queryDateFormat ')"]);
        $quoteGetInfo = $qGetInfoQuery->all();

        $apiStats = [];
            foreach ($communicationVoice as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);

                $apiStats[$item['timeLine']] = $item;
            }

            foreach ($communicationSms as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);
                $item['cMemV'] = (isset($item['cMemV']) ? $item['cMemV'] : 0);

                if (isset($apiStats[$item['timeLine']])) {
                    $apiStats[$item['timeLine']]['cSms'] = $item['cSms'];
                    $apiStats[$item['timeLine']]['cAvgTimeS'] = $item['cAvgTimeS'];
                    $apiStats[$item['timeLine']]['cMemS'] = $item['cMemS'];
                }else {
                    $apiStats[$item['timeLine']] = $item;
                }
            }

            foreach ($communicationEmail as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);

                if (isset($apiStats[$item['timeLine']])) {
                    $apiStats[$item['timeLine']]['cEmail'] = $item['cEmail'];
                    $apiStats[$item['timeLine']]['cAvgTimeE'] = $item['cAvgTimeE'];
                    $apiStats[$item['timeLine']]['cMemE'] = $item['cMemE'];
                }else {
                    $apiStats[$item['timeLine']] = $item;
                }
            }

            foreach ($leadCreate as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);

                if (isset($apiStats[$item['timeLine']])) {
                    $apiStats[$item['timeLine']]['lCreate'] = $item['lCreate'];
                    $apiStats[$item['timeLine']]['lAvgTimeC'] = $item['lAvgTimeC'];
                    $apiStats[$item['timeLine']]['lMemC'] = $item['lMemC'];
                }else {
                    $apiStats[$item['timeLine']] = $item;
                }
            }

            foreach ($leadSoldUpdate as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);

                if (isset($apiStats[$item['timeLine']])) {
                    $apiStats[$item['timeLine']]['leadSU'] = $item['leadSU'];
                    $apiStats[$item['timeLine']]['lAvgTimeSU'] = $item['lAvgTimeSU'];
                    $apiStats[$item['timeLine']]['lMemSU'] = $item['lMemSU'];
                }else {
                    $apiStats[$item['timeLine']] = $item;
                }
            }

            foreach ($quoteCreate as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);

                if (isset($apiStats[$item['timeLine']])) {
                    $apiStats[$item['timeLine']]['qCreate'] = $item['qCreate'];
                    $apiStats[$item['timeLine']]['qAvgTimeC'] = $item['qAvgTimeC'];
                    $apiStats[$item['timeLine']]['qMemC'] = $item['qMemC'];
                }else {
                    $apiStats[$item['timeLine']] = $item;
                }
            }

            foreach ($quoteUpdate as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);

                if (isset($apiStats[$item['timeLine']])) {
                    $apiStats[$item['timeLine']]['qUpdate'] = $item['qUpdate'];
                    $apiStats[$item['timeLine']]['qAvgTimeU'] = $item['qAvgTimeU'];
                    $apiStats[$item['timeLine']]['qMemU'] = $item['qMemU'];
                }else {
                    $apiStats[$item['timeLine']] = $item;
                }
            }

            foreach ($quoteGetInfo as $item) {
                $item['cSms'] = (isset($item['cSms']) ? $item['cSms'] : 0);
                $item['qUpdate'] = (isset($item['qUpdate']) ? $item['qUpdate'] : 0);
                $item['cEmail'] = (isset($item['cEmail']) ? $item['cEmail'] : 0);
                $item['lCreate'] = (isset($item['lCreate']) ? $item['lCreate'] : 0);
                $item['leadSU'] = (isset($item['leadSU']) ? $item['leadSU'] : 0);
                $item['qCreate'] = (isset($item['qCreate']) ? $item['qCreate'] : 0);
                $item['qInfo'] = (isset($item['qInfo']) ? $item['qInfo'] : 0);
                $item['cVoice'] = (isset($item['cVoice']) ? $item['cVoice'] : 0);

                if (isset($apiStats[$item['timeLine']])) {
                    $apiStats[$item['timeLine']]['qInfo'] = $item['qInfo'];
                    $apiStats[$item['timeLine']]['qAvgTimeI'] = $item['qAvgTimeI'];
                    $apiStats[$item['timeLine']]['qMemI'] = $item['qMemI'];
                }else {
                    $apiStats[$item['timeLine']] = $item;
                }
            }
        ksort($apiStats);
        return $apiStats;
    }
}
