<?php

namespace frontend\widgets\lead\editTool;

use common\models\Client;
use common\models\Department;
use common\models\Lead;
use common\models\Sources;
use yii\base\Model;

/**
 * Class Form
 *
 * @property int $leadId
 * @property int $client_id
 * @property int $source_id
 * @property string $request_ip
 * @property string $offset_gmt
 * @property int $discount_id
 * @property $final_profit
 * @property $tips
 * @property $agents_processing_fee
 * @property int $l_call_status_id
 * @property int $l_duplicate_lead_id
 * @property int $l_dep_id
 */
class Form extends Model
{
    public $leadId;
    public $client_id;
    public $source_id;
    public $request_ip;
    public $offset_gmt;
    public $discount_id;
    public $final_profit;
    public $tips;
    public $agents_processing_fee;
    public $l_call_status_id;
    public $l_duplicate_lead_id;
    public $l_dep_id;

    public function __construct(Lead $lead, $config = [])
    {
        parent::__construct($config);
        $this->leadId = $lead->id;
        $this->client_id = $lead->client_id;
        $this->source_id = $lead->source_id;
        $this->request_ip = $lead->request_ip;
        $this->offset_gmt = $lead->offset_gmt;
        $this->discount_id = $lead->discount_id;
        $this->final_profit = $lead->final_profit;
        $this->tips = $lead->tips;
        $this->agents_processing_fee = $lead->agents_processing_fee;
        $this->l_call_status_id = $lead->l_call_status_id;
        $this->l_duplicate_lead_id = $lead->l_duplicate_lead_id;
        $this->l_dep_id = $lead->l_dep_id;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['client_id', 'required'],
            ['client_id', 'integer'],
            ['client_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['client_id', 'exist', 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],

            ['source_id', 'required'],
            ['source_id', 'integer'],
            ['source_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['source_id', 'exist', 'targetClass' => Sources::class, 'targetAttribute' => ['source_id' => 'id']],

            ['request_ip', 'ip'],

            ['offset_gmt', 'string'],

            ['discount_id', 'integer'],
            ['discount_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['final_profit', 'number'],

            ['tips', 'number'],

            ['agents_processing_fee', 'number'],

            ['l_call_status_id', 'required'],
            ['l_call_status_id', 'integer'],
            ['l_call_status_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['l_call_status_id', 'in', 'range' => array_keys(Lead::CALL_STATUS_LIST)],

            ['l_duplicate_lead_id', 'integer'],
            ['l_duplicate_lead_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['l_duplicate_lead_id', 'validateDuplicate'],

            ['l_dep_id', 'required'],
            ['l_dep_id', 'integer'],
            ['l_dep_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['l_dep_id', 'in', 'range' => [Department::DEPARTMENT_SALES, Department::DEPARTMENT_EXCHANGE]],
        ];
    }

    public function validateDuplicate(): void
    {
        if (!$this->l_duplicate_lead_id) {
            return;
        }
        if (!$lead = Lead::findOne($this->l_duplicate_lead_id)) {
            $this->addError('l_duplicate_lead_id', 'Lead Id: ' . $this->l_duplicate_lead_id  . ' not found.');
            return;
        }
        if ($this->l_duplicate_lead_id === $this->leadId) {
            $this->addError('l_duplicate_lead_id', 'Cant select self.');
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'source_id' => 'Source',
            'l_call_status_id' => 'Call status',
            'l_duplicate_lead_id' => 'Duplicate lead Id',
            'l_dep_id' => 'Department'
        ];
    }
}
