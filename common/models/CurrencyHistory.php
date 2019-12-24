<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "currency_history".
 *
 * @property string $cur_his_code
 * @property float|null $cur_his_base_rate
 * @property float|null $cur_his_app_rate
 * @property float|null $cur_his_app_percent
 * @property string $cur_his_created
 * @property string|null $cur_his_main_created_dt
 * @property string|null $cur_his_main_updated_dt
 * @property string|null $cur_his_main_synch_dt
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
            [['cur_his_code', 'cur_his_created'], 'required'],
            [['cur_his_base_rate', 'cur_his_app_rate', 'cur_his_app_percent'], 'number'],
            [['cur_his_created', 'cur_his_main_created_dt', 'cur_his_main_updated_dt', 'cur_his_main_synch_dt'], 'safe'],
            [['cur_his_code'], 'string', 'max' => 3],
            [['cur_his_code'], 'unique'],
            [['cur_his_code', 'cur_his_created'], 'unique', 'targetAttribute' => ['cur_his_code', 'cur_his_created']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cur_his_code' => 'Cur His Code',
            'cur_his_base_rate' => 'Cur His Base Rate',
            'cur_his_app_rate' => 'Cur His App Rate',
            'cur_his_app_percent' => 'Cur His App Percent',
            'cur_his_created' => 'Cur His Created',
            'cur_his_main_created_dt' => 'Cur His Main Created Dt',
            'cur_his_main_updated_dt' => 'Cur His Main Updated Dt',
            'cur_his_main_synch_dt' => 'Cur His Main Synch Dt',
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
		$currencyHistory->cur_his_code = $currency->cur_code;
		$currencyHistory->cur_his_base_rate = $currency->cur_base_rate;
		$currencyHistory->cur_his_app_rate = $currency->cur_app_rate;
		$currencyHistory->cur_his_app_percent = $currency->cur_app_percent;
		$currencyHistory->cur_his_main_created_dt = $currency->cur_created_dt;
		$currencyHistory->cur_his_main_updated_dt = $currency->cur_updated_dt;
		$currencyHistory->cur_his_main_synch_dt = $currency->cur_synch_dt;
		$currencyHistory->cur_his_created = $dateToday;

		return $currencyHistory;
	}

	/**
	 * @param string $code
	 * @param string $createdDate
	 * @return $this|array|CurrencyHistory|\yii\db\ActiveRecord|null
	 */
	public function findOrCreateByPrimaryKeys(string $code, string $createdDate)
	{
		$currencyHistory = self::find()->where(['cur_his_code' => $code, 'cur_his_created' => $createdDate])->one();
		if ($currencyHistory) {
			return $currencyHistory;
		}

		return (new static());
	}
}
