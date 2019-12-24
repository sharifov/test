<?php

namespace common\models;

use Yii;

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
            [['ch_base_rate', 'ch_app_rate', 'ch_app_percent'], 'number'],
            [['ch_created_date', 'ch_main_created_dt', 'ch_main_updated_dt', 'ch_main_synch_dt'], 'safe'],
            [['ch_code'], 'string', 'max' => 3],
            [['ch_code', 'ch_created_date'], 'unique', 'targetAttribute' => ['ch_code', 'ch_created_date']],
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
