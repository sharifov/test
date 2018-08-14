<?php

namespace common\models;

use Yii;

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
 *
 *
 * @property Trip[] $trips
 */
class ApiLog extends \yii\db\ActiveRecord
{
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
            [['al_user_id'], 'integer'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrips()
    {
        return $this->hasMany(Trip::class, ['tr_api_log_id' => 'al_id']);
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
    public function endApiLog(array $responseData = [])
    {
        $this->al_response_data = @json_encode($responseData);
        $this->al_response_dt = date('Y-m-d H:i:s');

        if($this->save()) {
            $responseData['action']             = $this->al_action;
            $responseData['response_id']        = $this->al_id;
            $responseData['request_dt']         = $this->al_request_dt;
            $responseData['response_dt']        = $this->al_response_dt;

        } else {
            Yii::error(print_r($this->errors, true), 'API:book:info:ApiLog:save');
        }

        return $responseData;
    }

    /**
     * @return array
     */
    public static function getActionFilter()
    {
        $arr = [];
        $data = self::find()->select(["COUNT(*) AS cnt", "al_action"])
            ->where('al_action IS NOT NULL')
            //->andWhere("job_start_dt >= NOW() - interval '24 hour'")
            ->groupBy(["al_action"])
            ->orderBy('cnt DESC')->asArray()->all();

        if($data)
            foreach ($data as $v) {
                $arr[$v['al_action']] = $v['al_action'].' - ['.$v['cnt'].']';
            }

        return $arr;
    }
}
