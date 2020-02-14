<?php

namespace sales\model\user\entity\profit\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\user\entity\profit\UserProfit;

/**
 * Class UserProfitSearch
 * @package sales\model\user\entity\profit\search
 *
 * @property int $payment_id
 * @property int $payroll_id
 * @property float $base_amount
 * @property float $sum_profit_amount
 * @property float $sum_payment_amount
 * @property string $date
 */
class UserProfitSearch extends UserProfit
{
	/**
	 * @var int
	 */
	public $payment_id;

	/**
	 * @var int
	 */
	public $payroll_id;

	/**
	 * @var float
	 */
	public $base_amount;

	/**
	 * @var float
	 */
	public $sum_profit_amount;

	/**
	 * @var float
	 */
	public $sum_payment_amount;

	/**
	 * @var string
	 */
	public $date;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['up_id', 'up_user_id', 'up_lead_id', 'up_order_id', 'up_product_quote_id', 'up_percent', 'up_status_id', 'up_payroll_id', 'up_type_id'], 'integer'],
            [['up_profit', 'up_split_percent', 'up_amount'], 'number'],
            [['up_created_dt', 'up_updated_dt'], 'safe'],
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
    public function search($params): ActiveDataProvider
	{
        $query = UserProfit::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'up_id' => $this->up_id,
            'up_user_id' => $this->up_user_id,
            'up_lead_id' => $this->up_lead_id,
            'up_order_id' => $this->up_order_id,
            'up_product_quote_id' => $this->up_product_quote_id,
            'up_percent' => $this->up_percent,
            'up_profit' => $this->up_profit,
            'up_split_percent' => $this->up_split_percent,
            'up_amount' => $this->up_amount,
            'up_status_id' => $this->up_status_id,
            'date_format(up_created_dt, "%Y-%m-%d")' => $this->up_created_dt,
            'date_format(up_updated_dt, "%Y-%m-%d")' => $this->up_updated_dt,
            'up_payroll_id' => $this->up_payroll_id,
            'up_type_id' => $this->up_type_id,
        ]);

        return $dataProvider;
    }

	/**
	 * @param string $date
	 * @param int|null $userId
	 * @return array|UserProfitSearch[]
	 */
    public static function searchForCalcPayroll(string $date, int $userId = null): array
	{
		$query = self::find()->select([
			'u.up_user_id',
			'p.upt_id as payment_id',
			'up.up_base_amount as base_amount',
			'sum(distinct u.up_amount) as sum_profit_amount',
			'sum(distinct p.upt_amount) as sum_payment_amount',
			'date_format(u.up_created_dt, \'%Y-%M\') as `date`',
			'u.up_payroll_id as `payroll_id`'
		])->alias('u');

		$query->innerJoin('employees e', 'u.up_user_id = e.id')
			->innerJoin('user_params up', 'e.id = up.up_user_id')
			->innerJoin('user_payment p', 'e.id = p.upt_assigned_user_id and date_format(u.up_created_dt, \'%Y-%m\') = date_format(p.upt_date, \'%Y-%m\')');

		$query->where(['date_format(u.up_created_dt, \'%Y-%m\')' => $date]);

		if ($userId) {
			$query->andWhere(['u.up_user_id' => $userId]);
		}

		$query->groupBy(
			'u.up_user_id'
		);

		return $query->all();
	}
}
