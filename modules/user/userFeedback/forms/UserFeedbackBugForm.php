<?php

namespace modules\user\userFeedback\forms;

use yii\base\Model;

/**
 * Class UserFeedbackBugForm
 *
 * @property string $title
 * @property string|null $message
 * @property string|null $screenshot
 * @property string|null $data
 */
class UserFeedbackBugForm extends Model
{
    public $title;
    public $message;
    public $screenshot;
    public $data;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['message', 'screenshot', 'data'], 'string'],
            [['data_json'], 'safe'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Title',
            'message' => 'Message',
            'screenshot' => 'Screenshot',
            'data' => 'Data',
        ];
    }
}
