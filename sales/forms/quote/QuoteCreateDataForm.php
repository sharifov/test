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
    public int $lead_id = 0;

    public string $origin_search_data = '';

    public ?string $provider_project_key = null;

    public function rules(): array
    {
        return [
            [['lead_id', 'origin_search_data'], 'required'],
            [['lead_id'], 'filter', 'filter' => 'intval'],
            [['lead_id'], 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
            [['origin_search_data'], CheckJsonValidator::class],
            [['origin_search_data'], 'validateOriginSearchData'],
            [['provider_project_key'], 'string', 'max' => 50],
            [['provider_project_key'], 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['provider_project_key' => 'project_key']]
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
