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
 * @property int $bo_sale_id
 */
class LeadAdditionalInformation extends Model
{
    public $pnr;
    public $bo_sale_id;
    public $vtf_processed;
    public $tkt_processed;
    public $exp_processed;
    public $passengers = [];
    public $paxInfo = [];

    public function rules()
    {
        return [
            [['vtf_processed', 'tkt_processed', 'exp_processed', 'passengers', 'pnr', 'paxInfo',
                'bo_sale_id'], 'safe'],
        ];
    }
}