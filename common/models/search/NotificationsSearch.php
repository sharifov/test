<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Notifications;

/**
 * NotificationsSearch represents the model behind the search form about `common\models\Notifications`.
 */
class NotificationsSearch extends Notifications
{
    public $datetime_start;
    public $datetime_end;
    public $date_range;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime_start', 'datetime_end'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['n_id', 'n_user_id', 'n_type_id'], 'integer'],
            [['n_title', 'n_message', 'n_read_dt', 'n_created_dt'], 'safe'],
            [['n_new', 'n_deleted', 'n_popup', 'n_popup_show'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Notifications::find()->with('nUser');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['n_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(empty($this->n_created_dt) && isset($params['NotificationsSearch']['date_range'])){
            $query->andFilterWhere(['>=', 'n_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
                ->andFilterWhere(['<=', 'n_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        if ($this->n_created_dt) {
            $query->andFilterWhere(['>=', 'n_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->n_created_dt))])
                ->andFilterWhere(['<=', 'n_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->n_created_dt) + 3600 * 24)]);
        }

        if ($this->n_read_dt) {
            $query->andFilterWhere(['>=', 'n_read_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->n_read_dt))])
                ->andFilterWhere(['<=', 'n_read_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->n_read_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'n_id' => $this->n_id,
            'n_user_id' => $this->n_user_id,
            'n_type_id' => $this->n_type_id,
            'n_new' => $this->n_new,
            'n_deleted' => $this->n_deleted,
            'n_popup' => $this->n_popup,
            'n_popup_show' => $this->n_popup_show,
        ]);

        $query->andFilterWhere(['like', 'n_title', $this->n_title])
            ->andFilterWhere(['like', 'n_message', $this->n_message]);

        return $dataProvider;
    }
}
