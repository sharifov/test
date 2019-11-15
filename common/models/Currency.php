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
 * @property double $cur_rate
 * @property double $cur_system_rate
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
            [['cur_rate', 'cur_system_rate'], 'number'],
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
            'cur_rate' => 'Bank Rate',
            'cur_system_rate' => 'System Rate',
            'cur_enabled' => 'Enabled',
            'cur_default' => 'Default',
            'cur_sort_order' => 'Sort Order',
            'cur_created_dt' => 'Created Dt',
            'cur_updated_dt' => 'Updated Dt',
            'cur_synch_dt' => 'Synch Dt',
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
        $currencyResponse = $currencyService->getRate('USD');

        //VarDumper::dump($currencyResponse); exit;

        if($currencyResponse && isset($currencyResponse['data']['rates'])) {
            $curData = $currencyResponse['data'];

            //VarDumper::dump($curData['rates']); exit;

            foreach ($curData['rates'] as $curCode => $rateVal) {
                $curCode = substr($curCode, -3);

                if (!$curCode) {
                    continue;
                }

                $currency = self::findOne(['cur_code' => $curCode]);
                if (!$currency) {
                    continue;
                    $currency = new self();
                    $currency->cur_code = $curCode;
                    $currency->cur_default = false;
                    $currency->cur_enabled = false;
                    $data['created'][] = $currency->cur_code;
                } else {

                    $currency->cur_rate = (float) $rateVal;
                    $currency->cur_system_rate = (float) $rateVal;

                    if (isset($curData['updated'])) {
                        $date = \DateTime::createFromFormat(DATE_RFC3339, $curData['updated']);
                        $currency->cur_synch_dt = $date->format('Y-m-d H:i:s');
                    }

                    $data['updated'][] = $curCode;
                    if (!$currency->save()) {
                        Yii::error($curCode . ': ' . VarDumper::dumpAsString($currency->errors), 'Currency:synchronization:save');
                    }
                }

            }

        } else {
            $data['error'] = 'Not found response[data][rates]';
        }

        return $data;
    }


    /**
     * @return array
     */
    public static function getList() : array
    {
        $query = self::find()->where(['cur_enabled' => true])->orderBy(['etp_name' => SORT_ASC]);
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'etp_id', 'etp_name');
    }
}
