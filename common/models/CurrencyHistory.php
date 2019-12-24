<?php

namespace common\models;

use common\models\query\CurrencyHistoryQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "currency_history".
 *
 * @property string $ch_code
 * @property float|null $ch_base_rate
 * @property float|null $ch_app_rate
 * @property float|null $ch_app_percent
 * @property string $ch_created_date
 * @property string|null $ch_main_created_dt
 * @property string|null $ch_main_updated_dt
 * @property string|null $ch_main_synch_dt
 *
 * @property Currency $chCode
 */
class CurrencyHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ch_code', 'ch_created_date'], 'required'],
			[['ch_app_percent'], 'number', 'max' => 100],
			[['ch_base_rate', 'ch_app_rate'], 'number', 'max' => 1000],
            [['ch_created_date', 'ch_main_created_dt', 'ch_main_updated_dt', 'ch_main_synch_dt'], 'safe'],
            [['ch_code'], 'string', 'max' => 3],
            [['ch_code', 'ch_created_date'], 'unique', 'targetAttribute' => ['ch_code', 'ch_created_date']],
			[['ch_code'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['ch_code' => 'cur_code']],
		];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ch_code' => 'Code',
            'ch_base_rate' => 'Base Rate',
            'ch_app_rate' => 'App Rate',
            'ch_app_percent' => 'App Percent',
            'ch_created_date' => 'Created',
            'ch_main_created_dt' => 'Main Created Dt',
            'ch_main_updated_dt' => 'Main Updated Dt',
            'ch_main_synch_dt' => 'Main Synch Dt',
        ];
    }

	/**
	 * @return ActiveQuery
	 */
	public function getChCode(): ActiveQuery
	{
		return $this->hasOne(Currency::class, ['cur_code' => 'ch_code']);
	}

	/**
	 * {@inheritdoc}
	 * @return CurrencyHistoryQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new CurrencyHistoryQuery (static::class);
	}

	/**
	 * @param Currency $currency
	 * @return $this
	 */
    public function fillByCurrency(Currency $currency): self
	{
		$dateToday = date('Y-m-d');

		$currencyHistory = $this->findOrCreateByPrimaryKeys($currency->cur_code, $dateToday);
		$currencyHistory->ch_code = $currency->cur_code;
		$currencyHistory->ch_base_rate = $currency->cur_base_rate;
		$currencyHistory->ch_app_rate = $currency->cur_app_rate;
		$currencyHistory->ch_app_percent = $currency->cur_app_percent;
		$currencyHistory->ch_main_created_dt = $currency->cur_created_dt;
		$currencyHistory->ch_main_updated_dt = $currency->cur_updated_dt;
		$currencyHistory->ch_main_synch_dt = $currency->cur_synch_dt;
		$currencyHistory->ch_created_date = $dateToday;

		return $currencyHistory;
	}

	/**
	 * @param string $code
	 * @param string $createdDate
	 * @return $this|array|CurrencyHistory|\yii\db\ActiveRecord|null
	 */
	public function findOrCreateByPrimaryKeys(string $code, string $createdDate)
	{
		$currencyHistory = self::find()->where(['ch_code' => $code, 'ch_created_date' => $createdDate])->one();
		if ($currencyHistory) {
			return $currencyHistory;
		}

		return (new static());
	}
}
