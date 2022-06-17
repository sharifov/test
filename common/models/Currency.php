<?php

namespace common\models;

use common\models\query\CurrencyQuery;
use src\helpers\ErrorsToStringHelper;
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
 * @property float|null $cur_base_rate
 * @property float|null $cur_app_rate
 * @property float|null $cur_app_percent
 * @property int|null $cur_enabled
 * @property int|null $cur_default
 * @property int|null $cur_sort_order
 * @property string|null $cur_created_dt
 * @property string|null $cur_updated_dt
 * @property string|null $cur_synch_dt
 *
 * @property CurrencyHistory[] $currencyHistories
 * @property array $dataList
 */
class Currency extends ActiveRecord
{
    public const DEFAULT_CURRENCY = 'USD';
    public const DEFAULT_CURRENCY_CLIENT_RATE = 1.00;

    private const DEFAULT_CURRENCY_BASE_RATE = 1.00;
    private const CACHE_KEY = 'cache-currency-list';

    private array $dataList = [];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'currency';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['cur_code', 'cur_name', 'cur_symbol'], 'required'],
            [['cur_app_percent'], 'number', 'max' => 100],
            [['cur_base_rate', 'cur_app_rate'], 'number'],
            [['cur_enabled', 'cur_default'], 'boolean'],
            [['cur_sort_order'], 'integer'],
            [['cur_created_dt', 'cur_updated_dt', 'cur_synch_dt'], 'safe'],
            [['cur_code'], 'string', 'max' => 3],
            [['cur_symbol'], 'string', 'max' => 10],
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
     * @return string[]
     */
    public function attributeLabels(): array
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

    public function getLabelForCurEnabled(): string
    {
        $label = $this->getAttributeLabel('cur_enabled');

        if ($this->cur_code === self::getDefaultCurrencyCode()) {
            $label .= ' (default currency)';
        }

        return $label;
    }

    /**
     * {@inheritdoc}
     * @return CurrencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CurrencyQuery(static::class);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        self::clearCache();
        $currencyHistory = (new CurrencyHistory())->fillByCurrency($this);
        if (!$currencyHistory->save(false)) {
            Yii::error($currencyHistory->ch_code . ': ' . VarDumper::dumpAsString($currencyHistory->errors), 'Currency:synchronization:CurrencyHistory:save');
        }
    }

    /**
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public static function synchronization(): array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'error' => false
        ];

        $currencyService = Yii::$app->currency;
        $currencyResponse = $currencyService->getRate(true, self::DEFAULT_CURRENCY);

        // VarDumper::dump($currencyResponse); exit;

        if ($currencyResponse && isset($currencyResponse['data']['quotes'])) {
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
                        //$currency->cur_enabled = (bool) $curItem['isEnabled'];
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
                    $logData['message'] = ErrorsToStringHelper::extractFromModel($currency, ' ');
                    $logData['data'] = $curItem;
                    $logData['code'] = $currency->cur_code;

                    Yii::error($logData, 'Currency:synchronization:save');
                }
            }
            self::clearCache();
        } else {
            $data['error'] = 'Not found response[data][quotes]';
        }

        return $data;
    }

    /**
     * @return bool
     */
    public static function clearCache(): bool
    {
        Yii::$app->cache->delete(self::CACHE_KEY . '-all');
        return Yii::$app->cache->delete(self::CACHE_KEY);
    }

    /**
     * @param bool $all
     * @return array
     */
    public static function getList(bool $all = false): array
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_KEY . ($all ? '-all' : '');
        $data = $cache->get($key);

        if ($data === false) {
            $query = self::find()->select(['cur_code'])->orderBy(['cur_sort_order' => SORT_ASC]);
            if (!$all) {
                $query->where(['cur_enabled' => true]);
            }
            $data = $query->asArray()->all();
            $data = ArrayHelper::map($data, 'cur_code', 'cur_code');
            if ($data) {
                $cache->set($key, $data);
            }
        }
        return $data;
    }

    /**
     * @return array|Currency|null
     */
    public static function getDefaultCurrency()
    {
        return self::find()->where(['cur_default' => 1])->one();
    }

    /**
     * @return string
     */
    public static function getDefaultCurrencyCodeByDb(): string
    {
        return self::getDefaultCurrency()->cur_code ?? self::DEFAULT_CURRENCY;
    }

    /**
     * @return string
     */
    public static function getDefaultCurrencyCode(): string
    {
        return self::DEFAULT_CURRENCY;
    }

    /**
     * @return float
     */
    public static function getDefaultClientCurrencyRate(): float
    {
        return self::getDefaultCurrency()->cur_app_rate ?? self::DEFAULT_CURRENCY_CLIENT_RATE;
    }

    /**
     * @return float
     */
    public static function getDefaultBaseCurrencyRate(): float
    {
        return self::getDefaultCurrency()->cur_base_rate ?? self::DEFAULT_CURRENCY_BASE_RATE;
    }

    /**
     * @param string $code
     * @return float|null
     */
    public static function getBaseRateByCurrencyCode(string $code): ?float
    {
        return self::find()->where(['cur_code' => $code])->one()->cur_base_rate ?? null;
    }
}
