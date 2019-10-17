<?php

namespace common\models\local;

use yii\base\Model;

/**
 * Class LeadLogMessage
 *
 * @property $title
 * @property $message
 * @property $model
 * @property $oldParams
 * @property $newParams
 *
 */
class LeadLogMessage extends Model
{

    public $title;
    public $message;
    public $model = null;
    public $oldParams = [];
    public $newParams = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['title', 'safe'],
            ['message', 'safe'],
            ['model', 'safe'],
            ['oldParams', 'safe'],
            ['newParams', 'safe'],
        ];
    }

    /**
     * @return false|string
     */
    public function getMessage()
    {
        foreach ($this->oldParams as $item => $value) {
            if (in_array($item, $this->excludedAttributes(), true)) {
                unset($this->oldParams[$item], $this->newParams[$item]);
            }
        }

        return json_encode($this->attributes);
    }

    /**
     * @return array
     */
    protected function excludedAttributes(): array
    {
        return [
            'updated', 'created',
        ];
    }

}
