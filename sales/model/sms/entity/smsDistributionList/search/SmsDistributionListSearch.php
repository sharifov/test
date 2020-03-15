<?php

namespace sales\model\sms\entity\smsDistributionList\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\sms\entity\smsDistributionList\SmsDistributionList;

/**
 * SmsDistributionListSearch represents the model behind the search form of `sales\model\sms\entity\smsDistributionList\SmsDistributionList`.
 */
class SmsDistributionListSearch extends SmsDistributionList
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['sdl_id', 'sdl_com_id', 'sdl_project_id', 'sdl_client_id', 'sdl_status_id', 'sdl_priority', 'sdl_num_segments', 'sdl_created_user_id', 'sdl_updated_user_id'], 'integer'],
            [['sdl_phone_from', 'sdl_phone_to', 'sdl_text', 'sdl_start_dt', 'sdl_end_dt', 'sdl_error_message', 'sdl_message_sid', 'sdl_created_dt', 'sdl_updated_dt'], 'safe'],
            [['sdl_price'], 'number'],
        ];
    }

    /**
     * @return array
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
        $query = SmsDistributionList::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['sdl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 50,
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
            'sdl_id' => $this->sdl_id,
            'sdl_com_id' => $this->sdl_com_id,
            'sdl_project_id' => $this->sdl_project_id,
            'sdl_client_id' => $this->sdl_client_id,
            'sdl_start_dt' => $this->sdl_start_dt,
            'sdl_end_dt' => $this->sdl_end_dt,
            'sdl_status_id' => $this->sdl_status_id,
            'sdl_priority' => $this->sdl_priority,
            'sdl_num_segments' => $this->sdl_num_segments,
            'sdl_price' => $this->sdl_price,
            'sdl_created_user_id' => $this->sdl_created_user_id,
            'sdl_updated_user_id' => $this->sdl_updated_user_id,
            'sdl_created_dt' => $this->sdl_created_dt,
            'sdl_updated_dt' => $this->sdl_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'sdl_phone_from', $this->sdl_phone_from])
            ->andFilterWhere(['like', 'sdl_phone_to', $this->sdl_phone_to])
            ->andFilterWhere(['like', 'sdl_text', $this->sdl_text])
            ->andFilterWhere(['like', 'sdl_error_message', $this->sdl_error_message])
            ->andFilterWhere(['like', 'sdl_message_sid', $this->sdl_message_sid]);

        return $dataProvider;
    }
}
