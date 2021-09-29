<?php

namespace sales\model\leadUserConversion\form;

use common\models\Employee;
use common\models\Lead;
use yii\base\Model;

/**
 * Class LeadUserConversionAddForm
 *
 */
class LeadUserConversionAddForm extends Model
{
    public $leadId;
    public $userId;
    public $description;

    public function __construct(int $leadId, $config = [])
    {
        $this->leadId = $leadId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['leadId', 'required'],
            ['leadId', 'integer'],
            ['leadId', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id']],

            ['userId', 'required'],
            ['userId', 'integer'],
            ['userId', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['userId' => 'id']],

            ['description', 'string', 'max' => 100],
        ];
    }
}
