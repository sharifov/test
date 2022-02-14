<?php

namespace modules\user\userFeedback\forms;

use common\components\validators\CheckJsonValidator;
use modules\user\userFeedback\entity\UserFeedback;
use yii\base\Model;

class UserFeedbackUpdateForm extends Model
{
    public $uf_title;
    public $uf_message;
    public $uf_data_json;
    public $uf_type_id;
    public $uf_status_id;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uf_status_id'], 'in', 'range' => array_flip(UserFeedback::STATUS_LIST) ],
            [['uf_type_id'], 'in', 'range' => array_flip(UserFeedback::TYPE_LIST)],
            [['uf_title'], 'required'],
            [['uf_message', 'uf_data_json'], 'string'],
            [['uf_data_json'], 'safe'],
            [['uf_data_json'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['uf_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uf_title' => 'Title',
            'uf_message' => 'Message',
            'uf_data_json' => 'Data',
        ];
    }

    public function formName()
    {
        return 'UserFeedback';
    }
}
