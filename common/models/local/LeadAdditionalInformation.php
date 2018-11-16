<?php

namespace common\models\local;


use yii\base\Model;

/**
 * This is the model LeadAdditionalInformation.
 *
 * @property boolean $vtf_processed
 * @property boolean $tkt_processed
 * @property boolean $exp_processed
 * @property array $passengers
 * @property array $paxInfo
 * @property string $pnr
 */
class LeadAdditionalInformation extends Model
{
    public $pnr;
    public $vtf_processed;
    public $tkt_processed;
    public $exp_processed;
    public $passengers = [];
    public $paxInfo = [];

    public function rules()
    {
        return [
            [['vtf_processed', 'tkt_processed', 'exp_processed', 'passengers', 'pnr', 'paxInfo'], 'safe'],
        ];
    }
}