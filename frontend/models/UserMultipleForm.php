<?php

namespace frontend\models;

use common\models\Employee;
use yii\base\Model;

/**
 * UserMultipleForm form
 */
class UserMultipleForm extends Model
{
    public $user_list;
    public $up_call_expert_limit;
    public $status_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_list'], 'required'],
            [['up_call_expert_limit', 'status_id'], 'integer'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_list'             => 'Selected Users',
            'up_call_expert_limit'  => 'Call Expert Limit',
        ];
    }
}
