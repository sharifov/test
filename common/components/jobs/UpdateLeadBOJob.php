<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-05-10
 */

namespace common\components\jobs;

use common\components\BackOffice;
use common\models\Lead;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the model class for table "Lead".
 *
 * @property int $lead_id
 */

class UpdateLeadBOJob extends BaseObject implements JobInterface
{
    public $lead_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {

        try {
            if($this->lead_id) {
                $lead = Lead::findOne($this->lead_id);

                if ($lead) {
                    $data['lead'] = [
                        'uid'               => $lead->uid,
                        'gid'               => $lead->gid,
                        'market_info_id'    => $lead->source_id,
                        'status_id'            => $lead->status,
                    ];

                    $response = BackOffice::sendRequest2('lead/update-status', $data);

                    if ($response->isOk) {
                        $result = $response->data;
                        if ($result && isset($result['status']) && $result['status'] === 'Success') {
                            return true;
                        }

                        Yii::error(print_r($response->content, true), 'UpdateLeadBOJob:BackOffice:sendRequest2:notSuccess');
                    } else {
                        Yii::error(print_r($response->content, true), 'UpdateLeadBOJob:BackOffice:sendRequest2');
                    }
                }
            }

        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'UpdateLeadBOJob:execute:catch');
        }
        return false;
    }

    public function getTtr()
    {
        return 1 * 20;
    }

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }*/
}