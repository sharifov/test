<?php

namespace sales\model\conference\form;

use common\models\Conference;
use yii\base\Model;

class DebugForm extends Model
{
    public const ACTION_RAW_DATA = 'raw_data';
    public const ACTION_SHOW_HISTORY = 'show_history';
    public const ACTION_RECALCULATE = 'recalculate';

    public const ACTION_LIST = [
        self::ACTION_RAW_DATA => 'Get raw data',
        self::ACTION_SHOW_HISTORY => 'Show conference history',
        self::ACTION_RECALCULATE => 'Recalculate participant stats (Old data will remove)',
    ];

    public $conferenceSid;
    public $action;

    public function rules(): array
    {
        return [
            ['conferenceSid', 'required'],
            ['conferenceSid', 'string'],
            ['conferenceSid', 'trim'],
            ['conferenceSid', 'exist', 'skipOnError' => true, 'targetClass' => Conference::class, 'targetAttribute' => ['conferenceSid' => 'cf_sid']],

            ['action', 'required'],
            ['action', 'string'],
            ['action', 'in', 'range' => array_keys(self::ACTION_LIST)],
        ];
    }
}
