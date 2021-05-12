<?php

namespace sales\forms\quote;

use common\components\validators\CheckJsonValidator;
use common\models\Lead;
use common\models\Project;
use frontend\helpers\JsonHelper;
use yii\base\Model;

/**
 * Class QuoteCreateDataForm
 * @package sales\forms\quote
 *
 * @property int $lead_id
 * @property string $origin_search_data
 * @property null|string $provider_project_key
 */
class QuoteCreateDataForm extends Model
{
    public $lead_id;

    public $origin_search_data;

    public $provider_project_key;

    public function rules(): array
    {
        return [
            [['lead_id', 'origin_search_data'], 'required'],

            [['lead_id'], 'integer'],
            [['lead_id'], 'filter', 'filter' => 'intval'],

            [['provider_project_key'], 'string', 'max' => 50],

            [['origin_search_data'], 'string'],
            [['origin_search_data'], CheckJsonValidator::class],
            [['origin_search_data'], 'validateOriginSearchData'],

            [['lead_id'], 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id'], 'message' => 'Lead not found'],
            [['provider_project_key'], 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['provider_project_key' => 'project_key'], 'message' => 'Project not found']
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function validateOriginSearchData($attribute, $params, $validator, $value)
    {
        $json = JsonHelper::decode($value);

        if (empty($json)) {
            $this->addError($attribute, 'Origin search data is empty');
            return false;
        }

        return true;
    }
}
