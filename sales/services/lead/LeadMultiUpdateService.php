<?php

namespace sales\services\lead;

use Yii;
use common\models\Lead;
use common\models\Reason;
use frontend\models\LeadMultipleForm;

class LeadMultiUpdateService
{
    public function update(LeadMultipleForm $multipleForm, int $userId): array
    {
        $report = [];
        foreach ($multipleForm->lead_list as $lead_id) {

            $leadId = (int)$lead_id;

            if (!$lead = Lead::findOne($leadId)) {
                $report[] = 'Not found Lead: ' . $leadId;
                continue;
            }

            if (!$lead->isAvailableForMultiUpdate()) {
                $report[] = 'Lead: ' . $leadId . ' with status: ' . $lead->getStatusName() . ' is not available for MultiUpdate. Available only status: Processing, FollowUp, Hold, Trash, Snooze';
                continue;
            }
            
            $is_save = false;

            if ($multipleForm->employee_id) {
                if ($multipleForm->employee_id == -1) {
                    $lead->employee_id = null;
                } else {
                    $lead->employee_id = $multipleForm->employee_id;
                }
                $is_save = true;
            }

            if ($multipleForm->status_id) {
                $lead->status = $multipleForm->status_id;
                $is_save = true;
            }

            if ($multipleForm->rating) {
                $lead->rating = $multipleForm->rating;
                $is_save = true;
            }

            $reasonValue = null;

            if ($multipleForm->status_id && is_numeric($multipleForm->reason_id)) {


                if ($multipleForm->reason_id > 0) {
                    $reasonValue = Reason::getReasonByStatus($multipleForm->status_id, $multipleForm->reason_id);
                } else {
                    $reasonValue = $multipleForm->reason_description;
                }

                if ($reasonValue) {
                    $reason = new Reason();
                    $reason->employee_id = $userId;
                    $reason->lead_id = $lead->id;
                    $reason->reason = $reasonValue;
                    $reason->created = date('Y-m-d H:i:s');

                    if (!$reason->save()) {
                        Yii::error($reason->errors, 'LeadMultiUpdateService/Reason:save');
                    }
                }

            }

            if ($is_save) {
                $lead->save();
            }
            
        }
        return $report;
    }
}
