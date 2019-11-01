<?php

namespace common\models\search;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadPreferences;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GlobalLog;
use yii\db\Query;

/**
 * GlobalLogSearch represents the model behind the search form of `common\models\GlobalLog`.
 *
 * @property int $leadId
 */
class GlobalLogSearch extends GlobalLog
{
	public $leadId;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gl_id', 'gl_app_user_id', 'gl_obj_id', 'leadId', 'gl_action_type'], 'integer'],
            [['gl_app_id', 'gl_model', 'gl_old_attr', 'gl_new_attr', 'gl_created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = GlobalLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['gl_id' => SORT_DESC]],
			'pagination' => [
				'pageSize' => 20,
			],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'gl_id' => $this->gl_id,
            'gl_app_user_id' => $this->gl_app_user_id,
            'gl_obj_id' => $this->gl_obj_id,
            'gl_created_at' => $this->gl_created_at,
			'gl_model' => $this->gl_model,
			'gl_app_id' => $this->gl_app_id,
			'gl_action_type' => $this->gl_action_type
        ]);

        return $dataProvider;
    }

	/**
	 * @param $params
	 * @return ActiveDataProvider
	 */
	public function searchByLead($params): ActiveDataProvider
	{
		$query = self::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['pageSize' => 10],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'gl_obj_id' => $this->leadId,
		]);

		$subQuery = (new Query())->select(['cp.id'])
			->from(ClientPhone::tableName() . ' as cp')
			->join('JOIN', Client::tableName() . ' as client', 'client.id = cp.client_id')
			->join('JOIN', Lead::tableName() . ' as lead', 'lead.client_id = client.id and lead.id = :leadId', [':leadId' => $this->leadId] );
		$query->orFilterWhere(['IN', 'gl_obj_id', $subQuery]);

		$subQuery = (new Query())->select(['ce.id'])
			->from(ClientEmail::tableName() . ' as ce')
			->join('JOIN', Client::tableName() . ' as client', 'client.id = ce.client_id')
			->join('JOIN', Lead::tableName() . ' as lead', 'lead.client_id = client.id and lead.id = :leadId', [':leadId' => $this->leadId] );
		$query->orFilterWhere(['IN', 'gl_obj_id', $subQuery]);

		$subQuery = (new Query())->select(['c.id'])
			->from(Client::tableName() . ' as c')
			->join('JOIN', Lead::tableName() . ' as lead', 'lead.client_id = c.id and lead.id = :leadId', [':leadId' => $this->leadId] );
		$query->orFilterWhere(['IN', 'gl_obj_id', $subQuery]);

		$subQuery = (new Query())->select(['lp.id'])
			->from(LeadPreferences::tableName() . ' as lp')
			->where(['lead_id' => $this->leadId]);
		$query->orFilterWhere(['IN', 'gl_obj_id', $subQuery]);

		return $dataProvider;
	}
}
