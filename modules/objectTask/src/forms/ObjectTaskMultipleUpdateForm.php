<?php

namespace modules\objectTask\src\forms;

use modules\objectTask\src\entities\ObjectTask;
use yii\base\Model;

class ObjectTaskMultipleUpdateForm extends Model
{
    public $element_list;
    public $element_list_json;
    public $statusId;

    public function rules(): array
    {
        return [
            ['element_list_json', 'required'],
            ['element_list_json', 'safe'],
            ['element_list_json', 'filter', 'filter' => function ($value) {
                try {
                    $data = \yii\helpers\Json::decode($value);
                    if (!is_array($data)) {
                        $this->addError('element_list_json', 'Invalid JSON data for decode');
                        return null;
                    }

                    foreach ($data as $objectUuid) {
                        $elementExists = ObjectTask::find()
                            ->where([
                                'ot_uuid' => $objectUuid
                            ])
                            ->limit(1)
                            ->exists();

                        if ($elementExists === false) {
                            $this->addError('element_list_json', 'Not found Object UUID: ' . $objectUuid);

                            return null;
                        }
                    }

                    $this->element_list = $data;

                    return $value;
                } catch (\yii\base\Exception $e) {
                    $this->addError('element_list_json', $e->getMessage());
                    return null;
                }
            }],

            [['statusId'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'statusId' => 'Status',
        ];
    }
}
