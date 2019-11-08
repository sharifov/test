<?php

namespace common\models\search;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\Lead2;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use common\models\Quote;
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
		$query = self::find()->select('*');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['pageSize' => 10],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$queryLead = GlobalLog::find()->alias('gl')
			->where(['gl_obj_id' => $this->leadId])
			->andWhere(['or', ['gl_model' => Lead::class], ['gl_model' => Lead2::class]]);

		$queryQuote = GlobalLog::find()->alias('gl')
			->innerJoin('quotes AS q', 'gl.gl_obj_id = q.id')
			->where(['q.lead_id' => $this->leadId,  'gl.gl_model' => Quote::class]);

		$queryLeadPreferences = GlobalLog::find()->alias('gl')
			->innerJoin('lead_preferences AS lp', 'gl.gl_obj_id = lp.id')
			->where(['lp.lead_id' => $this->leadId,  'gl.gl_model' => LeadPreferences::class]);

		$queryLeadFlightSegments = GlobalLog::find()->alias('gl')
			->innerJoin('lead_flight_segments AS lfp', 'gl.gl_obj_id = lfp.id')
			->where(['lfp.lead_id' => $this->leadId,  'gl.gl_model' => LeadFlightSegment::class]);

		$queryClientPhone = GlobalLog::find()->alias('gl')
			->innerJoin('client_phone as cp', 'cp.id = gl.gl_obj_id')
			->innerJoin( 'clients as client', 'client.id = cp.client_id')
			->innerJoin( 'leads as l', 'l.client_id = client.id and l.id = :leadId and gl.gl_model = :glModelPhone', [':leadId' => $this->leadId, ':glModelPhone' => ClientPhone::class]);

		$queryClientEmail = GlobalLog::find()->alias('gl')
			->join('join', 'client_email as ce', 'ce.id = gl.gl_obj_id')
			->join('join', 'clients as client', 'client.id = ce.client_id')
			->join('join', 'leads as l', 'l.client_id = client.id and l.id = :leadId and gl.gl_model = :glModelEmail', [':leadId' => $this->leadId, ':glModelEmail' => ClientEmail::class]);

		$queryClient = GlobalLog::find()->alias('gl')
			->join('join', 'clients as client', 'client.id = gl.gl_obj_id')
			->join('join', 'leads as l', 'l.client_id = client.id and l.id = :leadId and gl.gl_model = :glModelClient', [':leadId' => $this->leadId, ':glModelClient' => Client::class]);

		$query->from(['tbl' => $queryLead->union($queryQuote)
										->union($queryLeadPreferences)
										->union($queryLeadFlightSegments)
										->union($queryClientPhone)
										->union($queryClientEmail)
										->union($queryClient)])
			->orderBy(['gl_id' => SORT_ASC]);


		return $dataProvider;
	}
}
