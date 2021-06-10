<?php

namespace sales\model\quoteLabel\entity;

use common\models\Quote;
use sales\model\flightQuoteLabelList\entity\FlightQuoteLabelList;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "quote_label".
 *
 * @property int $ql_quote_id
 * @property string $ql_label_key
 *
 * @property Quote $quote
 * @property FlightQuoteLabelList|null $flightQuoteLabelList
 */
class QuoteLabel extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['ql_quote_id', 'ql_label_key'], 'unique', 'targetAttribute' => ['ql_quote_id', 'ql_label_key']],

            ['ql_label_key', 'required'],
            ['ql_label_key', 'string', 'max' => 50],

            ['ql_quote_id', 'required'],
            ['ql_quote_id', 'integer'],
        ];
    }

    public function getQuote(): ActiveQuery
    {
        return $this->hasOne(Quote::class, ['id' => 'ql_quote_id']);
    }

    public function getFlightQuoteLabelList(): ActiveQuery
    {
        return $this->hasOne(FlightQuoteLabelList::class, ['fqll_label_key' => 'ql_label_key']);
    }

    public function attributeLabels(): array
    {
        return [
            'ql_quote_id' => 'Quote ID',
            'ql_label_key' => 'Label Key',
        ];
    }

    public static function find(): QuoteLabelScopes
    {
        return new QuoteLabelScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'quote_label';
    }

    public function getDescription(): ?string
    {
        if (!$this->flightQuoteLabelList) {
            return null;
        }
        return $this->flightQuoteLabelList->fqll_description ?? ($this->flightQuoteLabelList->fqll_origin_description ?? $this->ql_label_key);
    }

    public static function create(int $quoteId, string $labelKey): QuoteLabel
    {
        $model = new self();
        $model->ql_quote_id = $quoteId;
        $model->ql_label_key = $labelKey;
        return $model;
    }
}
