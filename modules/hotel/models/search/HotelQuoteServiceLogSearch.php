<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\HotelQuoteServiceLog;

/**
 * HotelQuoteServiceLogSearch represents the model behind the search form of `modules\hotel\models\HotelQuoteServiceLog`.
 */
class HotelQuoteServiceLogSearch extends HotelQuoteServiceLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hqsl_id', 'hqsl_hotel_quote_id', 'hqsl_action_type_id', 'hqsl_status_id', 'hqsl_created_user_id'], 'integer'],
            [['hqsl_message', 'hqsl_created_dt', 'hqsl_updated_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = HotelQuoteServiceLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'hqsl_id' => $this->hqsl_id,
            'hqsl_hotel_quote_id' => $this->hqsl_hotel_quote_id,
            'hqsl_action_type_id' => $this->hqsl_action_type_id,
            'hqsl_status_id' => $this->hqsl_status_id,
            'hqsl_created_user_id' => $this->hqsl_created_user_id,
            'hqsl_created_dt' => $this->hqsl_created_dt,
            'hqsl_updated_dt' => $this->hqsl_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'hqsl_message', $this->hqsl_message]);

        return $dataProvider;
    }
}
