<?php
namespace backend\models\form\leads;


use yii\base\Model;

/**
 * This is the model LeadRequestInformation.
 *
 * @property boolean $vtf_processed
 * @property boolean $tkt_processed
 * @property boolean $exp_processed
 * @property array $passengers
 * @property string $pnr
 */

class LeadAdditionalInformation extends Model
{
    public $pnr;
    public $vtf_processed;
    public $tkt_processed;
    public $exp_processed;
    public $passengers;

    public function rules()
    {
        return [
            [['vtf_processed', 'tkt_processed', 'exp_processed', 'passengers', 'pnr'], 'safe'],
        ];
    }
}