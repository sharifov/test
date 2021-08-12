<?php

namespace modules\flight\src\useCases\reprotectionCreate\form;

use common\components\validators\CheckJsonValidator;
use common\models\Project;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use yii\base\Model;

/**
 * Class ReprotectionCreateForm
 *
 * @property $booking_id
 * @property $is_automate
 * @property $flight_quote
 * @property $project_key
 *
 * @property Project|null $project
 */
class ReprotectionCreateForm extends Model
{
    public $booking_id;
    public $is_automate;
    public $flight_quote;
    public $project_key;

    private ?Project $project;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['project_key'], 'required'],
            [['project_key'], 'string', 'max' => 50],
            [['project_key'], 'detectProject'],

            [['is_automate'], 'boolean', 'strict' => true, 'trueValue' => true, 'falseValue' => false, 'skipOnEmpty' => true],
            [['is_automate'], 'default', 'value' => false],

            [['flight_quote'], CheckJsonValidator::class, 'skipOnEmpty' => true],

            //[['booking_id'], 'checkExistByHash'],
        ];
    }

    public function checkExistByHash($attribute)
    {
        $hash = FlightRequest::generateHashFromDataJson($this->getAttributes());
        if (FlightRequest::findOne(['fr_hash' => $hash])) {
            $this->addError($attribute, 'FlightRequest already exist. Hash(' . $hash . ')');
        }
    }

    public function detectProject($attribute)
    {
        if ($project = Project::findOne(['project_key' => $this->project_key])) {
            $this->project = $project;
        } else {
            $this->addError($attribute, 'Project not found (' . $this->project_key . ')');
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }
}
