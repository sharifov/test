<?php

namespace src\model\voip\phoneDevice\log;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use yii\data\ActiveDataProvider;

/**
 * Class PhoneDeviceLogSearch
 *
 * @property $show_fields
 * @property $after_timestamp
 */
class PhoneDeviceLogSearch extends PhoneDeviceLog
{
    public $show_fields = [
        'pdl_id',
        'pdl_user_id',
        'pdl_device_id',
        'pdl_level',
        'pdl_message',
        'pdl_timestamp_dt',
    ];

    public $after_timestamp;

    public function rules(): array
    {
        return [
            ['pdl_id', 'integer'],

            ['pdl_user_id', 'integer'],

            ['pdl_device_id', 'integer'],

            ['pdl_level', 'integer'],

            ['pdl_message', 'string'],

            ['pdl_timestamp_dt', 'datetime', 'format' => 'php:Y-m-d'],

            ['pdl_created_dt', 'datetime', 'format' => 'php:Y-m-d'],

            ['after_timestamp', 'datetime', 'format' => 'php:Y-m-d H:i'],

            ['show_fields', IsArrayValidator::class],
            ['show_fields', 'each', 'rule' => ['in', 'range' => array_keys($this->getViewFields())], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['pdl_stacktrace', 'safe'],
        ];
    }

    public function isVisible(string $attribute): bool
    {
        return in_array($attribute, $this->show_fields, true);
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find()->with('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->pdl_created_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'pdl_created_dt', $this->pdl_created_dt, $user->timezone);
        }

        if ($this->pdl_timestamp_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'pdl_timestamp_dt', $this->pdl_timestamp_dt, null);
        }
        if ($this->after_timestamp) {
            \src\helpers\query\QueryHelper::dateMoreThanByUserTZ($query, 'pdl_timestamp_dt', $this->after_timestamp, null);
        }

        $query->andFilterWhere([
            'pdl_id' => $this->pdl_id,
            'pdl_user_id' => $this->pdl_user_id,
            'pdl_device_id' => $this->pdl_device_id,
            'pdl_level' => $this->pdl_level,
        ]);

        $query->andFilterWhere(['like', 'pdl_message', $this->pdl_message]);

        return $dataProvider;
    }

    public function getViewFields(): array
    {
        return [
            'pdl_id' => 'ID',
            'pdl_user_id' => 'User',
            'pdl_device_id' => 'Device ID',
            'pdl_level' => 'Level',
            'pdl_message' => 'Message',
            'pdl_error' => 'Error',
            'pdl_stacktrace' => 'Stacktrace',
            'pdl_timestamp_dt' => 'Timestamp',
            'pdl_created_dt' => 'Created',
        ];
    }
}
