<?php

namespace modules\flight\src\entities\flightQuoteLabel;

use modules\flight\models\FlightQuote;
use sales\model\flightQuoteLabelList\entity\FlightQuoteLabelList;
use sales\model\flightQuoteLabelList\service\FlightQuoteLabelListService;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "flight_quote_label".
 *
 * @property int $fql_quote_id
 * @property string $fql_label_key
 *
 * @property FlightQuote $fqlQuote
 * @property FlightQuoteLabelList|null $flightQuoteLabelList
 */
class FlightQuoteLabel extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['fql_quote_id', 'fql_label_key'], 'unique', 'targetAttribute' => ['fql_quote_id', 'fql_label_key']],

            ['fql_label_key', 'required'],
            ['fql_label_key', 'string', 'max' => 50],
            ['ql_label_key', 'in', 'range' => array_keys(FlightQuoteLabelListService::getListKeyDescription())],

            ['fql_quote_id', 'required'],
            ['fql_quote_id', 'integer'],
            ['fql_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['fql_quote_id' => 'fq_id']],
        ];
    }

    public function getFqlQuote(): ActiveQuery
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'fql_quote_id']);
    }

    public function getFlightQuoteLabelList(): ActiveQuery
    {
        return $this->hasOne(FlightQuoteLabelList::class, ['fqll_label_key' => 'fql_label_key']);
    }

    public function attributeLabels(): array
    {
        return [
            'fql_quote_id' => 'Quote ID',
            'fql_label_key' => 'Label Key',
        ];
    }

    public static function find(): FlightQuoteLabelScopes
    {
        return new FlightQuoteLabelScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_label';
    }

    public function getDescription(): ?string
    {
        if (!$this->flightQuoteLabelList) {
            return null;
        }
        return $this->flightQuoteLabelList->fqll_description ?? ($this->flightQuoteLabelList->fqll_origin_description ?? $this->fql_label_key);
    }

    public static function create(int $quoteId, string $labelKey)
    {
        $model = new self();
        $model->fql_quote_id = $quoteId;
        $model->fql_label_key = $labelKey;
        return $model;
    }
}
