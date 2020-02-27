<?php

namespace modules\qaTask\src\useCases\qaTask\decide\noAction;

use modules\qaTask\src\useCases\qaTask\QaTaskActionForm;

/**
 * Class QaTaskDecideNoActionForm
 *
 * @property string|null $description
 */
class QaTaskDecideNoActionForm extends QaTaskActionForm
{
    public $description;

    public function rules(): array
    {
        return [
            ['description', 'default', 'value' => null],
            ['description', 'string', 'max' => 235],
        ];
    }

    public function getComment(): string
    {
        $comment = 'No Action';
        if ($this->description) {
            $comment .= ': ' . $this->description;
        }
        return $comment;
    }

    public function attributeLabels(): array
    {
        return [
            'description' => 'Description',
        ];
    }
}
