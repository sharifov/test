<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "currency".
 *
 * @property string $cur_code
 * @property string $cur_name
 * @property string $cur_symbol
 * @property double $cur_base_rate
 * @property double $cur_app_rate
 * @property double $cur_app_percent
 * @property bool $cur_enabled
 * @property bool $cur_default
 * @property int $cur_sort_order
 * @property string $cur_created_dt
 * @property string $cur_updated_dt
 * @property string $cur_synch_dt
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cur_code', 'cur_name', 'cur_symbol'], 'required'],
            [['cur_app_percent'], 'number', 'max' => 100],
            [['cur_base_rate', 'cur_app_rate'], 'number', 'max' => 1000],
            [['cur_enabled', 'cur_default'], 'boolean'],
            [['cur_sort_order'], 'integer'],
            [['cur_created_dt', 'cur_updated_dt', 'cur_synch_dt'], 'safe'],
            [['cur_code', 'cur_symbol'], 'string', 'max' => 3],
            [['cur_name'], 'string', 'max' => 34],
            [['cur_code'], 'unique'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cur_created_dt', 'cur_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cur_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cur_code' => 'Code',
            'cur_name' => 'Name',
            'cur_symbol' => 'Symbol',
            'cur_base_rate' => 'Base Rate',
            'cur_app_rate' => 'App Rate',
            'cur_app_percent' => 'App Percent',
            'cur_enabled' => 'Enabled',
            'cur_default' => 'Default',
            'cur_sort_order' => 'Sort Order',
            'cur_created_dt' => 'Created Date',
            'cur_updated_dt' => 'Updated Date',
            'cur_synch_dt' => 'Synch Date',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CurrencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CurrencyQuery(get_called_class());
    }

	/**
	 * @param bool $insert
	 * @param array $changedAttributes
	 */
    public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);
		$currencyHistory = (new CurrencyHistory())->fillByCurrency($this);
		if (!$currencyHistory->save(false)) {
			Yii::error($currencyHistory->cur_his_code . ': ' . VarDumper::dumpAsString($currencyHistory->errors), 'Currency:synchronization:CurrencyHistory:save');
		}
	}

    /**
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public static function synchronization() : array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'error' => false
        ];

        $currencyService = Yii::$app->currency;
        $currencyResponse = $currencyService->getRate(true, 'USD');

        // VarDumper::dump($currencyResponse); exit;

        if($currencyResponse && isset($currencyResponse['data']['quotes'])) {
            $curData = $currencyResponse['data'];



            //VarDumper::dump($curData['rates']); exit;

//            foreach ($curData['extra'] as $curCode => $rateVal) {
//                $curCode = substr($curCode, -3);
//
//                if (!$curCode) {
//                    continue;
//                }
//
//                $currency = self::findOne(['cur_code' => $curCode]);
//                if (!$currency) {
//                    continue;
//                    $currency = new self();
//                    $currency->cur_code = $curCode;
//                    $currency->cur_default = false;
//                    $currency->cur_enabled = false;
//                    $data['created'][] = $currency->cur_code;
//                } else {
//
//                    $currency->cur_app_rate = (float) $rateVal;
//                    $currency->cur_base_rate = (float) $rateVal;
//                    //$currency->cur_base_rate = (float) $rateVal;
//
//                    if (isset($curData['updated'])) {
//                        $date = \DateTime::createFromFormat(DATE_RFC3339, $curData['updated']);
//                        $currency->cur_synch_dt = $date->format('Y-m-d H:i:s');
//                    }
//
//                    $data['updated'][] = $curCode;
//                }
//
//            }


            foreach ($curData['quotes'] as $curItem) {

                if (!isset($curItem['code'])) {
                    continue;
                }

                $currency = self::findOne(['cur_code' => $curItem['code']]);
                if (!$currency) {

                    $currency = new self();
                    $currency->cur_code = $curItem['code'];
                    $currency->cur_name = $curItem['name'];
                    $currency->cur_symbol = $curItem['symbol'];

                    if (isset($curItem['sort'])) {
                        $currency->cur_sort_order = $curItem['sort'];
                    }

                    if (isset($curItem['isDefault'])) {
                        $currency->cur_default = $curItem['isDefault'];
                    }

                    if (isset($curItem['isEnabled'])) {
                        $currency->cur_enabled = $curItem['isEnabled'];
                    }

                    $data['created'][] = $currency->cur_code;

                } else {

                    if ($currency->cur_name !== $curItem['name']) {
                        $currency->cur_name = $curItem['name'];
                    }

                    if ($currency->cur_symbol !== $curItem['symbol']) {
                        $currency->cur_symbol = $curItem['symbol'];
                    }

                    if (isset($curItem['isDefault']) && $currency->cur_default !== $curItem['isDefault']) {
                        $currency->cur_default = (bool) $curItem['isDefault'];
                    }

                    if (isset($curItem['isEnabled']) && $currency->cur_enabled !== $curItem['isEnabled']) {
                        $currency->cur_enabled = (bool) $curItem['isEnabled'];
                    }

                    if (isset($curItem['sort']) && $currency->cur_sort_order !== $curItem['sort']) {
                        $currency->cur_sort_order = (int) $curItem['sort'];
                    }

                    $data['updated'][] = $currency->cur_code;
                }

                if (isset($curData['updated'])) {
                    $date = \DateTime::createFromFormat(DATE_RFC3339, $curData['updated']);
                    $currency->cur_synch_dt = $date->format('Y-m-d H:i:s');
                }

                $currency->cur_base_rate = (float) $curItem['bankRate'];
                $currency->cur_app_rate = round((float) $curItem['systemRate'], 5);
                $currency->cur_app_percent = (float) $curItem['rateReservePercent'];


                if (!$currency->save()) {
                    Yii::error($currency->cur_code . ': ' . VarDumper::dumpAsString($currency->errors), 'Currency:synchronization:save');
                }

            }


        } else {
            $data['error'] = 'Not found response[data][quotes]';
        }

        return $data;
    }


    /**
     * @return array
     */
    public static function getList() : array
    {
        $query = self::find()->where(['cur_enabled' => true])->orderBy(['cur_sort_order' => SORT_ASC]);
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'cur_code', 'cur_code');
    }
}
