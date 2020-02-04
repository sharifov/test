<?php

namespace modules\offer\src\entities\offerViewLog\search;

use common\models\Employee;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerViewLog\OfferViewLog;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class OfferViewLogCrudSearch extends OfferViewLog
{
    public function rules(): array
    {
        return [
            ['ofvwl_id', 'integer'],
            ['ofvwl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['ofvwl_id' => 'ofvwl_id']],

            ['ofvwl_offer_id', 'integer'],
            ['ofvwl_offer_id', 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['ofvwl_offer_id' => 'of_id']],

            ['ofvwl_visitor_id', 'string', 'max' => 32],

            ['ofvwl_ip_address', 'string', 'max' => 40],

            ['ofvwl_user_agent', 'string', 'max' => 255],

            ['ofvwl_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['offer']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ofvwl_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ofvwl_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'ofvwl_created_dt', $this->ofvwl_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'ofvwl_id' => $this->ofvwl_id,
            'ofvwl_offer_id' => $this->ofvwl_offer_id,
        ]);

        $query->andFilterWhere(['like', 'ofvwl_visitor_id', $this->ofvwl_visitor_id]);
        $query->andFilterWhere(['like', 'ofvwl_ip_address', $this->ofvwl_ip_address]);
        $query->andFilterWhere(['like', 'ofvwl_user_agent', $this->ofvwl_user_agent]);

        return $dataProvider;
    }
}
