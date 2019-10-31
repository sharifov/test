<?php

namespace sales\entities\log;

use common\models\ApiUser;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadPreferences;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "global_logs".
 *
 * @property int $gl_id
 * @property string $gl_app_id
 * @property int $gl_app_user_id
 * @property string $gl_model
 * @property int $gl_obj_id
 * @property array $gl_old_attr
 * @property array $gl_new_attr
 * @property array $gl_formatted_attr
 * @property string $gl_created_at
 *
 * @property Employee|ApiUser|null $user
 */
class GlobalLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'global_log';
    }

	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['gl_created_at']
				],
				'value' => date('Y-m-d H:i:s')
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['gl_app_id', 'gl_model', 'gl_obj_id'], 'required'],
            [['gl_app_user_id', 'gl_obj_id'], 'integer'],
            [['gl_old_attr', 'gl_new_attr', 'gl_formatted_attr', 'gl_created_at'], 'safe'],
            [['gl_app_id'], 'string', 'max' => 20],
            [['gl_model'], 'string', 'max' => 50],
        ];
    }

	/**
	 * @param string $glModel
	 * @param int $glObjectId
	 * @param string $glAppId
	 * @param int|null $glAppUserId
	 * @param string|null $glOldAttr
	 * @param string|null $glNewAttr
	 * @param string|null $glFormattedAttr
	 * @return static
	 */
    public static function create(
		string $glModel,
		int $glObjectId,
    	string $glAppId,
		?int $glAppUserId,
		?string $glOldAttr,
		?string $glNewAttr,
		?string $glFormattedAttr
	): self
	{
		$log = new static();
		$log->gl_app_id = $glAppId;
		$log->gl_app_user_id = $glAppUserId;
		$log->gl_model = $glModel;
		$log->gl_obj_id = $glObjectId;
		$log->gl_old_attr = $glOldAttr;
		$log->gl_new_attr = $glNewAttr;
		$log->gl_formatted_attr = $glFormattedAttr;
		return $log;
	}

	public function getGlModel()
	{
		return (new \ReflectionClass($this->gl_model))->getShortName();
	}

	/**
	 * @return ActiveQuery|null
	 */
	public function getUser(): ?ActiveQuery
	{
		if ($this->gl_app_id === 'app-frontend') {
			return $this->hasOne(Employee::class, ['id' => 'gl_app_user_id']);
		}

		if ($this->gl_app_id === 'app-webapi') {
			return $this->hasOne(ApiUser::class, ['au_id' => 'gl_app_user_id']);
		}

		return null;
	}

	public function getGeneralLeadLog(int $leadId)
	{
		$query = GlobalLog::find()->andWhere(['IN','gl_obj_id',$leadId]);

		$subQuery = (new Query())->select(['cp.id'])
			->from(ClientPhone::tableName() . ' as cp')
			->join('JOIN', Client::tableName() . ' as client', 'client.id = cp.client_id')
			->join('JOIN', Lead::tableName() . ' as lead', 'lead.client_id = client.id and lead.id = :leadId', [':leadId' => $leadId] );
		$query->orWhere(['IN', 'gl_obj_id', $subQuery]);

		$subQuery = (new Query())->select(['ce.id'])
			->from(ClientEmail::tableName() . ' as ce')
			->join('JOIN', Client::tableName() . ' as client', 'client.id = ce.client_id')
			->join('JOIN', Lead::tableName() . ' as lead', 'lead.client_id = client.id and lead.id = :leadId', [':leadId' => $leadId] );
		$query->orWhere(['IN', 'gl_obj_id', $subQuery]);

		$subQuery = (new Query())->select(['c.id'])
			->from(Client::tableName() . ' as c')
			->join('JOIN', Lead::tableName() . ' as lead', 'lead.client_id = c.id and lead.id = :leadId', [':leadId' => $leadId] );
		$query->orWhere(['IN', 'gl_obj_id', $subQuery]);

		$subQuery = (new Query())->select(['lp.id'])
			->from(LeadPreferences::tableName() . ' as lp')
			->where(['lead_id' => $leadId]);
		$query->orWhere(['IN', 'gl_obj_id', $subQuery]);

		return $query->all();
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'gl_id' => 'Id',
            'gl_app_id' => 'Application',
            'gl_app_user_id' => 'Who made the changes',
            'gl_model' => 'Model',
            'gl_obj_id' => 'Object id',
            'gl_old_attr' => 'Old attributes',
            'gl_new_attr' => 'New attributes',
            'gl_formatted_attr' => 'Formatted Attributes',
            'gl_created_at' => 'When changes were made',
			'glModel' => 'Model'
        ];
    }
}
