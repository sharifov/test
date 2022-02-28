<?php

namespace src\model\phoneNumberRedial\useCase\createMultiple;

use common\models\Employee;
use common\models\Project;
use src\model\phoneList\entity\PhoneList;
use common\components\validators\IsArrayValidator;

/**
 * Class CreateMultipleForm
 * @package src\model\phoneNumberRedial\useCase\createMultiple
 *
 * @property int $enabled
 * @property string|null $name
 * @property array $phonePattern
 * @property array $phoneNumber
 * @property int|null $priority
 * @property int|null $projectId
 */
class CreateMultipleForm extends \yii\base\Model
{
    public $enabled;
    public $name;
    public $phonePattern;
    public $phoneNumber;
    public $priority;
    public $projectId;

    public function rules(): array
    {
        return [
            ['enabled', 'integer'],

            ['name', 'string', 'max' => 255],

            ['phonePattern', 'required'],
            ['phonePattern', IsArrayValidator::class],
            ['phonePattern', 'each', 'rule' => ['string', 'max' => 30]],
            ['phonePattern', 'each', 'rule' => ['trim']],

            ['phoneNumber', 'required'],
            ['phoneNumber', IsArrayValidator::class],
            ['phoneNumber', 'each', 'rule' => ['exist', 'targetClass' => PhoneList::class, 'targetAttribute' => ['phoneNumber' => 'pl_id']]],

            ['priority', 'integer'],
            ['priority', 'filter', 'filter' => 'intval'],

            ['projectId', 'required'],
            ['projectId', 'integer'],
            ['projectId', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['projectId' => 'id']],
        ];
    }
}
