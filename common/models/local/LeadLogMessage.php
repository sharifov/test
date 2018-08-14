<?php
namespace common\models\local;

use yii\base\Model;

class LeadLogMessage extends Model
{
    public $title;
    public $message;
    public $model = null;
    public $oldParams = [];
    public $newParams = [];

    public function rules()
    {
        return [
            [['oldParams','newParams', 'title', 'message', 'model'], 'safe'],
        ];
    }

    public function getMessage()
    {
        foreach ($this->oldParams as $item => $value) {
            if (in_array($item, $this->excludedAttributes())) {
                unset($this->oldParams[$item]);
                unset($this->newParams[$item]);
            }
        }

        return json_encode($this->attributes);
    }

    protected function excludedAttributes()
    {
        return [
            'updated', 'created',
        ];
    }
}