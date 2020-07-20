<?php

namespace sales\model\clientChat\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\ClientChat;

/**
 * ClientChatQaSearch represents the model behind the search form of `ClientChat`.
 *
 * @property $createdRangeDate
 * @property string|null $dataCountry
 * @property string|null $dataCity
 */
class ClientChatQaSearch extends ClientChat
{
    public $createdRangeDate;
    public $dataCountry;
    public $dataCity;

    public function rules(): array
    {
        return [
            [
                [
                    'cch_id', 'cch_ccr_id', 'cch_project_id',
                    'cch_dep_id', 'cch_channel_id', 'cch_client_id',
                    'cch_owner_user_id', 'cch_case_id', 'cch_lead_id',
                    'cch_status_id', 'cch_ua', 'cch_created_user_id',
                    'cch_updated_user_id', 'cch_client_online'
                ],
                'integer'
            ],
            [
                [
                    'cch_rid', 'cch_title', 'cch_description',
                    'cch_note', 'cch_ip', 'cch_language_id',
                    'cch_created_dt', 'cch_updated_dt', 'createdRangeDate',
                    'dataCountry', 'dataCity',
                ],
                'safe'
            ],
        ];
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
        $query = ClientChat::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cch_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cch_id' => $this->cch_id,
            'cch_ccr_id' => $this->cch_ccr_id,
            'cch_project_id' => $this->cch_project_id,
            'cch_dep_id' => $this->cch_dep_id,
            'cch_channel_id' => $this->cch_channel_id,
            'cch_client_id' => $this->cch_client_id,
            'cch_owner_user_id' => $this->cch_owner_user_id,
            'cch_case_id' => $this->cch_case_id,
            'cch_lead_id' => $this->cch_lead_id,
            'cch_status_id' => $this->cch_status_id,
            'cch_ua' => $this->cch_ua,
            'cch_created_dt' => $this->cch_created_dt,
            'cch_updated_dt' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
            'cch_client_online' => $this->cch_client_online,
        ]);

        if ($this->createdRangeDate) {
			$dateRange = explode(' - ', $this->createdRangeDate);
			if ($dateRange[0] && $dateRange[1]) {
				$fromDate = date('Y-m-d', strtotime($dateRange[0]));
				$toDate = date('Y-m-d', strtotime($dateRange[1]));
				$query->andWhere(['BETWEEN', 'DATE(cch_created_dt)', $fromDate, $toDate]);
			}
		}

        $query->andFilterWhere(['like', 'cch_rid', $this->cch_rid])
            ->andFilterWhere(['like', 'cch_title', $this->cch_title])
            ->andFilterWhere(['like', 'cch_description', $this->cch_description])
            ->andFilterWhere(['like', 'cch_note', $this->cch_note])
            ->andFilterWhere(['like', 'cch_ip', $this->cch_ip])
            ->andFilterWhere(['like', 'cch_language_id', $this->cch_language_id]);

        return $dataProvider;
    }
}
