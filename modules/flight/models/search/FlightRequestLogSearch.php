<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightRequestLog;

/**
 * FlightRequestLogSearch represents the model behind the search form of `modules\flight\models\FlightRequestLog`.
 */
class FlightRequestLogSearch extends FlightRequestLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flr_id', 'flr_fr_id', 'flr_status_id_old', 'flr_status_id_new'], 'integer'],
            [['flr_description', 'flr_created_dt', 'flr_updated_dt'], 'safe'],
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
        $query = FlightRequestLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['flr_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'flr_id' => $this->flr_id,
            'flr_fr_id' => $this->flr_fr_id,
            'flr_status_id_old' => $this->flr_status_id_old,
            'flr_status_id_new' => $this->flr_status_id_new,
            'flr_created_dt' => $this->flr_created_dt,
            'flr_updated_dt' => $this->flr_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'flr_description', $this->flr_description]);

        return $dataProvider;
    }
}
