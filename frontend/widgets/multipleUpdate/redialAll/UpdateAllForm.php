<?php

namespace frontend\widgets\multipleUpdate\redialAll;

use common\models\Lead;
use common\models\Project;
use yii\base\Model;

/**
 * Class UpdateAllForm
 *
 * @property $projectId
 * @property $statusId
 * @property $weight
 * @property $remove
 */
class UpdateAllForm extends Model
{
    public $projectId;
    public $statusId;
    public $weight;
    public $remove;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['projectId', 'exist', 'targetClass' => Project::class, 'targetAttribute' => 'id'],
            ['projectId', 'validateEmpty', 'skipOnEmpty' => false],
            ['projectId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['statusId', 'in', 'range' => array_keys(Lead::STATUS_LIST)],
            ['statusId', 'validateEmpty', 'skipOnEmpty' => false],
            ['statusId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['weight', 'integer', 'max' => 10000],
            ['weight', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['weight', function () {
                if (!$this->remove && $this->isNotSelectedWeight()) {
                    $this->addError('weight', 'Weight or Remove cannot be blank');
                }
            }, 'skipOnEmpty' => false],

            ['remove', 'boolean'],
            ['remove', function () {
                if (!$this->remove && $this->isNotSelectedWeight()) {
                    $this->addError('remove', 'Remove or Weight cannot be blank');
                }
            }],
        ];
    }

    /**
     * @return bool
     */
    public function isNotSelectedWeight(): bool
    {
        return $this->weight === '' || $this->weight === null;
    }

    public function validateEmpty($attribute): void
    {
        if (!$this->projectId && !$this->statusId) {
            $this->addError($attribute, 'Project or Status cannot be blank');
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'projectId' => 'Project',
            'statusId' => 'Status',
            'remove' => 'Remove',
        ];
    }

    /**
     * @return bool
     */
    public function isRemove(): bool
    {
        return $this->remove ? true : false;
    }
}
