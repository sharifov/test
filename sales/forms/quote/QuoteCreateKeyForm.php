<?php

namespace sales\forms\quote;

use common\models\Lead;
use common\models\Project;
use yii\base\Model;

/**
 * Class QuoteCreateKeyForm
 * @package sales\forms\quote
 *
 * @property int $lead
 * @property string $offer_search_key
 * @property string|null $provider_project_key
 */
class QuoteCreateKeyForm extends Model
{
    public int $lead_id = 0;

    public string $offer_search_key = '';

    public ?string $provider_project_key = null;

    public function rules(): array
    {
        return [
            [['lead_id', 'offer_search_key'], 'required'],
            [['lead_id'], 'filter', 'filter' => 'intval'],

            [['offer_search_key'], 'string', 'max' => 255],
            [['provider_project_key'], 'string', 'max' => 50],

            [['lead_id'], 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
            [['provider_project_key'], 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['provider_project_key' => 'project_key']]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
