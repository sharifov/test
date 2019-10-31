<?php

namespace sales\entities\log;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadPreferences;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GlobalLogSearch
 * @package sales\entities\log
 *
 * @property int $leadId
 * @property string $gl_model
 */
class GlobalLogSearch extends GlobalLog
{
	public $leadId;

	/**
	 * @return array
	 */
    public function rules(): array
    {
        return [
            [['gl_id', 'gl_app_user_id', 'gl_obj_id', 'leadId'], 'integer'],
            [['gl_app_id', 'gl_model', 'gl_old_attr', 'gl_new_attr', 'gl_formatted_attr', 'gl_created_at'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
	{
        $query = GlobalLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
        ]);

        $query->andFilterWhere(['like', 'gl_app_id', $this->gl_app_id])
            ->andFilterWhere(['like', 'gl_model', $this->gl_model])
            ->andFilterWhere(['like', 'gl_old_attr', $this->gl_old_attr])
            ->andFilterWhere(['like', 'gl_new_attr', $this->gl_new_attr])
            ->andFilterWhere(['like', 'gl_formatted_attr', $this->gl_formatted_attr]);

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

//		$query->andFilterWhere(['like', 'gl_model', $this->gl_model]);

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
