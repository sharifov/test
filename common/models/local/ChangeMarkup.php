<?php
namespace common\models\local;

use common\models\LeadLog;
use common\models\Quote;
use yii\base\Exception;
use yii\base\Model;

/**
 * ChangeMarkup model
 *
 * @property string $quote_uid
 * @property string $pax_type
 * @property string $value
 * @property Quote $quote
 */
class ChangeMarkup extends Model
{
    const
        PAX_ADT = 'adt-markup',
        PAX_CNN = 'cnn-markup',
        PAX_INF = 'inf-markup';

    public $quote_uid;
    public $pax_type;
    public $value;

    public $quote;

    public function rules()
    {
        return [
            [['quote_uid', 'pax_type', 'value'], 'required'],
            ['pax_type', 'in', 'range' => [self::PAX_ADT, self::PAX_CNN, self::PAX_INF]],
            [['quote'], 'safe'],
        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();
        $this->quote = Quote::findOne(['uid' => $this->quote_uid]);
        if ($this->quote === null) {
            $this->addError('quote_uid', 'Quote Uid is invalid');
        }
    }

    public function change()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $errors = [];
            $result = [
                'uid' => $this->quote_uid,
                'total' => [
                    'sellingPrice' => 0,
                    'markup' => 0
                ],
                'actual' => [
                    'sellingPrice' => 0,
                    'markup' => 0
                ]
            ];
            $cnt = $oldMarkup = 0;
            $changedAttributes['selling'] = $this->quote->quotePrice()['selling'];
            foreach ($this->quote->quotePrices as $quotePrice) {
                $oldMarkup += $quotePrice->extra_mark_up;
                $quotePrice->toFloat();
                $quotePrice->roundValue();
                if ($quotePrice->passenger_type == $quotePrice::PASSENGER_ADULT && $this->pax_type == self::PAX_ADT) {
                    $quotePrice->extra_mark_up = $this->value;
                    $quotePrice->selling = $quotePrice->net + $quotePrice->mark_up + $quotePrice->extra_mark_up;
                    $result['actual']['sellingPrice'] += $quotePrice->selling;
                    $result['actual']['markup'] += $quotePrice->extra_mark_up;
                    $cnt++;
                } else if ($quotePrice->passenger_type == $quotePrice::PASSENGER_CHILD && $this->pax_type == self::PAX_CNN) {
                    $quotePrice->extra_mark_up = $this->value;
                    $quotePrice->selling = $quotePrice->net + $quotePrice->mark_up + $quotePrice->extra_mark_up;
                    $result['actual']['sellingPrice'] += $quotePrice->selling;
                    $result['actual']['markup'] += $quotePrice->extra_mark_up;
                    $cnt++;
                } else if ($quotePrice->passenger_type == $quotePrice::PASSENGER_INFANT && $this->pax_type == self::PAX_INF) {
                    $quotePrice->extra_mark_up = $this->value;
                    $quotePrice->selling = $quotePrice->net + $quotePrice->mark_up + $quotePrice->extra_mark_up;
                    $result['actual']['sellingPrice'] += $quotePrice->selling;
                    $result['actual']['markup'] += $quotePrice->extra_mark_up;
                    $cnt++;
                }

                $quotePrice->roundValue();
                if (!$quotePrice->save()) {
                    $errors[] = $quotePrice->getErrors();
                }

                $result['total']['sellingPrice'] += $quotePrice->selling;
                $result['total']['markup'] += $quotePrice->extra_mark_up;
            }

            $result['total']['sellingPrice'] = round($result['total']['sellingPrice'], 2);
            $result['total']['markup'] = round($result['total']['markup'], 2);
            $result['actual']['markup'] = round(($result['actual']['markup'] / $cnt), 2);
            $result['actual']['sellingPrice'] = round(($result['actual']['sellingPrice'] / $cnt), 2);

            //Add logs after changed model attributes
            $leadLog = new LeadLog((new LeadLogMessage()));
            $leadLog->logMessage->oldParams = $changedAttributes;
            $newParams['selling'] = round($result['total']['sellingPrice'], 2);
            $leadLog->logMessage->newParams = $newParams;
            $leadLog->logMessage->title = 'Update';
            $leadLog->logMessage->model = sprintf('%s (%s)', $this->quote->formName(), $this->quote->uid);
            $leadLog->addLog([
                'lead_id' => $this->quote->lead_id,
            ]);

            if (!empty($errors)) {
                $this->addError('quote_uid', $errors);
            } else {
                $transaction->commit();
                return $result;
            }
        } catch (Exception $ex) {
            $transaction->rollBack();
            $this->addError('quote_uid', $ex->getMessage());
        }
        return [];
    }
}