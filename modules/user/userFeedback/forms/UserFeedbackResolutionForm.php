<?php

namespace modules\user\userFeedback\forms;

use modules\user\userFeedback\entity\UserFeedback;
use yii\base\Model;

class UserFeedbackResolutionForm extends Model
{
    public $uf_resolution;
    public $uf_status_id;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uf_resolution'], 'string', 'max' => 500],
            [['uf_status_id'], 'in', 'range' => array_keys(UserFeedback::FINAL_STATUS_LIST)],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uf_resolution' => 'Resolution',
            'uf_status_id' => 'Status'
        ];
    }

    public function formName()
    {
        return 'UserFeedback';
    }
}
