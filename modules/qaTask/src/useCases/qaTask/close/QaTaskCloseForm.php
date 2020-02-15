<?php

namespace modules\qaTask\src\useCases\qaTask\close;

use modules\qaTask\src\entities\qaTask\QaTaskRating;
use modules\qaTask\src\useCases\qaTask\QaTaskActionForm;

/**
 * Class QaTaskCloseForm
 *
 * @property string|null $description
 * @property int|null $rating
 */
class QaTaskCloseForm extends QaTaskActionForm
{
    public $description;
    public $rating;

    public function rules(): array
    {
        return [
            ['rating', 'required'],
            ['rating', 'integer'],
            ['rating', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['rating', 'in', 'range' => array_keys($this->getRatingList())],

            ['description', 'string', 'max' => 255],
        ];
    }

    public function getRatingList(): array
    {
        return QaTaskRating::getList();
    }

    public function attributeLabels(): array
    {
        return [
            'description' => 'Description',
            'rating' => 'Rating',
        ];
    }
}
