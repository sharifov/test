<?php

namespace frontend\models;

use yii\base\Model;


/**
 * Class SmsInboxForm
 * @package frontend\models
 *
 * @property integer $last_n
 * @property string $action
 * @property string $last_date
 *
 */

class SmsInboxForm extends Model
{
    /**
     * @var $action string
     */
    public $action;

    /**
     * @var $last_n int
     */
    public $last_n;

    /**
     * @var $last_date string
     */
    public $last_date;

    public function rules()
    {
        return [
            [['action'], 'required'],
            [['last_n'], 'integer'],
            [['last_date'], 'string', 'max' => 10, 'min' => 10],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'last_n'         => 'Limit items',
            'last_date'        => 'Date From',
        ];
    }

}