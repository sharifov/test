<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteSearchCid;

/**
 * QuoteSearchCidSearch represents the model behind the search form of `common\models\QuoteSearchCid`.
 */
class QuoteSearchCidSearch extends QuoteSearchCid
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['qsc_id', 'qsc_q_id'], 'integer'],
            [['qsc_cid'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = QuoteSearchCid::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qsc_id' => $this->qsc_id,
            'qsc_q_id' => $this->qsc_q_id,
        ]);

        $query->andFilterWhere(['like', 'qsc_cid', $this->qsc_cid]);

        return $dataProvider;
    }
}
