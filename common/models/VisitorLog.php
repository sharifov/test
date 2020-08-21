<?php

namespace common\models;

use common\models\query\VisitorLogQuery;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%visitor_log}}".
 *
 * @property int $vl_id
 * @property int|null $vl_project_id
 * @property string|null $vl_source_cid
 * @property string|null $vl_ga_client_id
 * @property string|null $vl_ga_user_id
 * @property int|null $vl_customer_id
 * @property int|null $vl_client_id
 * @property int|null $vl_lead_id
 * @property string|null $vl_gclid
 * @property string|null $vl_dclid
 * @property string|null $vl_utm_source
 * @property string|null $vl_utm_medium
 * @property string|null $vl_utm_campaign
 * @property string|null $vl_utm_term
 * @property string|null $vl_utm_content
 * @property string|null $vl_referral_url
 * @property string|null $vl_location_url
 * @property string|null $vl_user_agent
 * @property string|null $vl_ip_address
 * @property string|null $vl_visit_dt
 * @property string|null $vl_created_dt
 * @property int|null $vl_cvd_id
 *
 * @property Client $client
 * @property Lead $lead
 * @property Project $project
 */
class VisitorLog extends \yii\db\ActiveRecord
{
    public const SCENARIO_API_CREATE = 'api_create';
    public const SCENARIO_CLIENT_CHAT_CREATE = 'create_by_client_chat';

    public static function tableName(): string
    {
        return '{{%visitor_log}}';
    }

    public function rules(): array
    {
        return [
            ['vl_project_id', 'required', 'on' => [self::SCENARIO_DEFAULT]],
            ['vl_project_id', 'integer'],
            ['vl_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['vl_project_id' => 'id']],

            ['vl_customer_id', 'integer'],

            ['vl_client_id', 'integer'],
            ['vl_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['vl_client_id' => 'id']],

            ['vl_lead_id', 'required', 'on' => [self::SCENARIO_DEFAULT]],
            ['vl_lead_id', 'integer'],
            ['vl_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['vl_lead_id' => 'id']],

            ['vl_visit_dt', 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_API_CREATE]],
            ['vl_visit_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['vl_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['vl_source_cid', 'string', 'max' => 10],

            ['vl_ga_client_id', 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_API_CREATE]],
            ['vl_ga_client_id', 'string', 'max' => 36],

            ['vl_ga_user_id', 'string', 'max' => 36],

            ['vl_gclid', 'string', 'max' => 100],

            ['vl_dclid', 'string', 'max' => 255],

            ['vl_utm_source', 'string', 'max' => 50],

            ['vl_utm_medium', 'string', 'max' => 50],

            ['vl_utm_campaign', 'string', 'max' => 50],

            ['vl_utm_term', 'string', 'max' => 50],

            ['vl_utm_content', 'string', 'max' => 50],

            ['vl_referral_url', 'string', 'max' => 500],

            ['vl_location_url', 'string', 'max' => 500],

            ['vl_user_agent', 'string', 'max' => 500],

            ['vl_ip_address', 'string', 'max' => 39],

            ['vl_cvd_id', 'integer'],
			['vl_cvd_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatVisitorData::class, 'targetAttribute' => ['vl_cvd_id' => 'cvd_id']],
		];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['vl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_API_CREATE] = [
            'vl_source_cid', 'vl_ga_client_id', 'vl_ga_user_id', 'vl_customer_id', 'vl_gclid', 'vl_dclid',
            'vl_utm_source', 'vl_utm_medium', 'vl_utm_campaign', 'vl_utm_term', 'vl_utm_content', 'vl_referral_url',
            'vl_location_url', 'vl_user_agent', 'vl_ip_address', 'vl_visit_dt',
        ];
        $scenarios[self::SCENARIO_CLIENT_CHAT_CREATE] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    public function attributeLabels(): array
    {
        return [
            'vl_id' => 'ID',
            'vl_project_id' => 'Project ID',
            'vl_Project' => 'Project',
            'vl_source_cid' => 'Source Cid',
            'vl_ga_client_id' => 'Ga Client ID',
            'vl_ga_user_id' => 'Ga User ID',
            'vl_customer_id' => 'Customer ID',
            'vl_client_id' => 'Client ID',
            'vl_lead_id' => 'Lead ID',
            'vl_gclid' => 'Gclid',
            'vl_dclid' => 'Dclid',
            'vl_utm_source' => 'Utm Source',
            'vl_utm_medium' => 'Utm Medium',
            'vl_utm_campaign' => 'Utm Campaign',
            'vl_utm_term' => 'Utm Term',
            'vl_utm_content' => 'Utm Content',
            'vl_referral_url' => 'Referral Url',
            'vl_location_url' => 'Location Url',
            'vl_user_agent' => 'User Agent',
            'vl_ip_address' => 'Ip Address',
            'vl_visit_dt' => 'Visit Dt',
            'vl_created_dt' => 'Created Dt',
            'vl_cvd_id' => 'Client Chat Visitor Data',
        ];
    }

    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'vl_client_id']);
    }

    public function getLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'vl_lead_id']);
    }

    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'vl_project_id']);
    }

    public static function getVisitorLogsByLead(int $leadId): array
    {
        return self::find()->limitFields()->byLead($leadId)->asArray()->all();
    }

    public static function getVisitorLog(int $id): array
    {
        return self::find()->limitFields()->byId($id)->asArray()->one();
    }

    public static function find(): VisitorLogQuery
    {
        return new VisitorLogQuery(static::class);
    }

    public static function createByClientChatRequest(int $cvdId, array $data): self
	{
		$log = new self();
		$log->scenario = self::SCENARIO_CLIENT_CHAT_CREATE;
		$log->vl_cvd_id = $cvdId;
		self::fillInData($log, $data);
		return $log;
	}

	public function updateByClientChatRequest(array $data): void
	{
		$this->scenario = self::SCENARIO_CLIENT_CHAT_CREATE;
		self::fillInData($this, $data);
	}

	private static function fillInData(self $_self, $data): void
	{
		$_self->vl_source_cid = $data['sources']['cid'] ?? null;
		$_self->vl_utm_source = $data['sources']['utm_source'] ?? null;
		$_self->vl_ga_client_id = $data['visitor']['ga_client_id'] ?? null;
		$_self->vl_gclid = $data['sources']['gclid'] ?? null;
		$_self->vl_dclid = $data['sources']['dclid'] ?? null;
		$_self->vl_utm_source = $data['sources']['utm_source'] ?? null;
		$_self->vl_utm_medium = $data['sources']['utm_medium'] ?? null;
		$_self->vl_utm_content = $data['sources']['utm_content'] ?? null;
		$_self->vl_utm_term = $data['sources']['utm_term'] ?? null;
		$_self->vl_utm_campaign = $data['sources']['utm_campaign'] ?? null;
		$_self->vl_user_agent = $data['system']['user_agent'] ?? null;
		$_self->vl_ip_address = $data['geo']['ip'] ?? null;
	}
}
