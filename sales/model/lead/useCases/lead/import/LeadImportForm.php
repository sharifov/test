<?php

namespace sales\model\lead\useCases\lead\import;

use common\models\Project;
use common\models\Sources;
use sales\forms\CompositeForm;

/**
 * Class LeadImportForm
 *
 * @property ClientForm $client
 * @property integer $rating
 * @property string $notes
 * @property string $marketing_info_id
 * @property integer $project_id
 * @property integer $source_id
 */
class LeadImportForm extends CompositeForm
{
    public $rating;
    public $notes;
    public $marketing_info_id;
    public $project_id;

    public $source_id;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->client = new ClientForm();
    }

    public function rules(): array
    {
        return [
            ['rating', 'default', 'value' => null],
            ['rating', 'integer'],
            ['rating', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['rating', 'filter', 'filter' => function () {
                if ($this->rating > 3 || $this->rating < 1) {
                    return null;
                }
                return $this->rating;
            }, 'skipOnEmpty' => true],

            ['notes', 'string'],

            ['project_id', 'required'],
            ['project_id', 'integer'],
            ['project_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],

            ['marketing_info_id', 'required'],
            ['marketing_info_id', 'string'],
            ['marketing_info_id', 'validateMarketingInfoId', 'skipOnError' => true],
        ];
    }

    public function validateMarketingInfoId(): void
    {
        if (!$source = Sources::find()->byCid($this->marketing_info_id)->one()) {
            $this->addError('marketing_info_id', 'Marketing Info is invalid.');
            return;
        }
        if ($this->project_id && $this->project_id !== $source->project_id) {
            $this->addError('project_id', 'Source project ID must be equals projectId.');
            return;
        }
        $this->source_id = $source->id;
    }

    protected function internalForms(): array
    {
        return ['client'];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'marketing_info_id' => 'Marketing Info',
            'project_id' => 'Project ID',
            'notes' => 'Notes',
            'rating' => 'Rating',
        ];
    }
}
