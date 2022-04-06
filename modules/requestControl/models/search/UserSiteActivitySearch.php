<?php

namespace modules\requestControl\models\search;

use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\requestControl\models\UserSiteActivity;
use yii\db\Expression;

/**
 * UserSiteActivitySearch represents the model behind the search form of `frontend\models\UserSiteActivity`.
 *
 * @property string $createTimeRange
 * @property int $createTimeStart
 * @property int $createTimeEnd
 */
class UserSiteActivitySearch extends UserSiteActivity
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usa_id', 'usa_user_id', 'usa_request_type'], 'integer'],
            [['usa_request_url', 'usa_page_url', 'usa_ip', 'usa_request_get', 'usa_request_post'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['usa_created_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
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
        $query = UserSiteActivity::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['usa_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dateTimeStart = $dateTimeEnd = null;

        if ($this->createTimeStart) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
        }

        if ($this->createTimeEnd) {
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
        }

        $query->andFilterWhere(['>=', 'usa_created_dt', $dateTimeStart])
            ->andFilterWhere(['<=', 'usa_created_dt', $dateTimeEnd]);


        $query->andFilterWhere(['=','DATE(usa_created_dt)', $this->usa_created_dt]);


        // grid filtering conditions
        $query->andFilterWhere([
            'usa_id' => $this->usa_id,
            'usa_user_id' => $this->usa_user_id,
            'usa_request_type' => $this->usa_request_type,
            //'usa_created_dt' => $this->usa_created_dt,
        ]);

        $query->andFilterWhere(['like', 'usa_request_url', $this->usa_request_url])
            ->andFilterWhere(['like', 'usa_page_url', $this->usa_page_url])
            ->andFilterWhere(['like', 'usa_ip', $this->usa_ip])
            ->andFilterWhere(['like', 'usa_request_get', $this->usa_request_get])
            ->andFilterWhere(['like', 'usa_request_post', $this->usa_request_post]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function searchReport($params)
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $dateTimeStart = $dateTimeEnd = null;

        $query = UserSiteActivity::find()->select(['cnt' => 'COUNT(*)', 'created_date' => 'DATE(usa_created_dt)', 'created_hour' => 'HOUR(usa_created_dt)'])
            ->groupBy(['DATE(usa_created_dt)', 'HOUR(usa_created_dt)'])
            ->orderBy(['created_date' => SORT_ASC, 'created_hour' => SORT_ASC])
            ->limit(100);

        $query->andFilterWhere(['usa_user_id' => $this->usa_user_id]);


        if ($this->createTimeStart) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
        }

        if ($this->createTimeEnd) {
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
        }

        $query->andFilterWhere(['>=', 'usa_created_dt', $dateTimeStart])
            ->andFilterWhere(['<=', 'usa_created_dt', $dateTimeEnd]);

        $dataHour = $query->asArray()->all();


        $query = UserSiteActivity::find()->select(['cnt' => 'COUNT(*)', 'user_id' => 'usa_user_id'])
            ->groupBy(['usa_user_id'])
            ->orderBy(['cnt' => SORT_DESC])
            ->limit(20);

        $query->andFilterWhere(['usa_user_id' => $this->usa_user_id]);

        if ($this->createTimeStart) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
        }

        if ($this->createTimeEnd) {
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
        }

        $query->andFilterWhere(['>=', 'usa_created_dt', $dateTimeStart])
            ->andFilterWhere(['<=', 'usa_created_dt', $dateTimeEnd]);

        $dataUsers = $query->asArray()->all();


        $query = UserSiteActivity::find()->select(['cnt' => 'COUNT(*)', 'page_url' => 'usa_page_url'])
            //->innerJoinWith(['usaUser'])
            ->where(['!=', 'usa_page_url', ''])
            ->andWhere(['!=', 'usa_page_url', 'site/index'])
            ->groupBy(['usa_page_url']);
        //->orderBy(['cnt' => SORT_DESC])
        //->limit(20);

        $query->andFilterWhere(['usa_user_id' => $this->usa_user_id]);

        if ($this->createTimeStart) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
        }

        if ($this->createTimeEnd) {
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
        }

        $query->andFilterWhere(['>=', 'usa_created_dt', $dateTimeStart])
            ->andFilterWhere(['<=', 'usa_created_dt', $dateTimeEnd]);

        $subQuery = UserSiteActivity::find()->select(['cnt' => 'COUNT(*)', 'page_url' => new Expression('"site/index"')])
            ->where(['=', 'usa_page_url', ''])
            ->orWhere(['=', 'usa_page_url', 'site/index']);

        $subQuery->andFilterWhere(['usa_user_id' => $this->usa_user_id]);

        if ($this->createTimeStart) {
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC($this->createTimeStart);
        }

        if ($this->createTimeEnd) {
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC($this->createTimeEnd);
        }

        $subQuery->andFilterWhere(['>=', 'usa_created_dt', $dateTimeStart])
            ->andFilterWhere(['<=', 'usa_created_dt', $dateTimeEnd]);

        $unionQuery = (new \yii\db\Query())
            ->from(['unitedTbl' => $query->union($subQuery)])
            ->orderBy(['cnt' => SORT_DESC])
            ->limit(20);

        $dataPages = $unionQuery->createCommand()->queryAll();


        $data['byHour'] = $dataHour;
        $data['byPage'] = $dataPages;
        $data['byUser'] = $dataUsers;

        return $data;
    }
}
