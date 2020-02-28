<?php

namespace common\models;

use common\models\query\VisitorLogQuery;
use Yii;

/**
 * This is the model class for table "{{%visitor_log}}".
 *
 * @property int $vl_id
 * @property int|null $vl_project_id
 * @property string|null $vl_source_cid
 * @property string|null $vl_ga_client_id
 * @property string|null $vl_ga_user_id
 * @property int|null $vl_user_id
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
 *
 * @property Client $vlClient
 * @property Lead $vlLead
 * @property Project $vlProject
 */
class VisitorLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%visitor_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vl_project_id', 'vl_user_id', 'vl_client_id', 'vl_lead_id'], 'integer'],
            [['vl_visit_dt', 'vl_created_dt'], 'safe'],
            [['vl_source_cid'], 'string', 'max' => 10],
            [['vl_ga_client_id', 'vl_ga_user_id'], 'string', 'max' => 36],
            [['vl_gclid'], 'string', 'max' => 100],
            [['vl_dclid'], 'string', 'max' => 255],
            [['vl_utm_source', 'vl_utm_medium', 'vl_utm_campaign', 'vl_utm_term', 'vl_utm_content'], 'string', 'max' => 50],
            [['vl_referral_url', 'vl_location_url', 'vl_user_agent'], 'string', 'max' => 500],
            [['vl_ip_address'], 'string', 'max' => 39],
            [['vl_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['vl_client_id' => 'id']],
            [['vl_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::className(), 'targetAttribute' => ['vl_lead_id' => 'id']],
            [['vl_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['vl_project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vl_id' => 'ID',
            'vl_project_id' => 'Project ID',
            'vl_Project' => 'Project',
            'vl_source_cid' => 'Source Cid',
            'vl_ga_client_id' => 'Ga Client ID',
            'vl_ga_user_id' => 'Ga User ID',
            'vl_user_id' => 'User ID',
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
        ];
    }

    /**
     * Gets query for [[VlClient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVlClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'vl_client_id']);
    }

    /**
     * Gets query for [[VlLead]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVlLead()
    {
        return $this->hasOne(Lead::className(), ['id' => 'vl_lead_id']);
    }

    /**
     * Gets query for [[VlProject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVlProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'vl_project_id']);
    }

    /**
     * {@inheritdoc}
     * @return VisitorLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VisitorLogQuery(get_called_class());
    }
}
