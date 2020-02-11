<?php

namespace modules\hotel\src\entities\hotelQuoteServiceLog\search;

use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HotelQuoteServiceLogCrudSearch represents the model behind the search form of `modules\hotel\models\HotelQuoteServiceLog`.
 */
class HotelQuoteServiceLogCrudSearch extends HotelQuoteServiceLog
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['hqsl_id', 'hqsl_hotel_quote_id', 'hqsl_action_type_id', 'hqsl_status_id', 'hqsl_created_user_id'], 'integer'],
            [['hqsl_message', 'hqsl_created_dt', 'hqsl_updated_dt'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @param int $hotelQuoteId
     * @return ActiveDataProvider
     */
    public function search($params, int $hotelQuoteId = 0): ActiveDataProvider
    {
        $query = HotelQuoteServiceLog::find()->orderBy(['hqsl_id' => SORT_DESC]);

        if ($hotelQuoteId) {
            $query->andWhere(['hqsl_hotel_quote_id' => $hotelQuoteId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

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
