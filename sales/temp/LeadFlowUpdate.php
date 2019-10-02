<?php

namespace sales\temp;

use common\models\Lead2;
use common\models\LeadFlow;
use yii\helpers\Json;

class LeadFlowUpdate
{

    public static function update(Lead2 $lead, $debug = false): array
    {

//        $logs = $lead->getLeadLogs()->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->asArray()->all();
        $logs = $lead->leadLogs;

//        $leadFlows = $lead->getLeadFlows()->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created')->all();
        $leadFlows = $lead->leadFlows;

//        $reasons = $lead->getReasons()->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created')->asArray()->all();
        $reasons = $lead->reasons;

        $statuses = self::createStatuses($logs);

        if ($debug) {
            echo 'Status before: ' . PHP_EOL;
            echo self::renderStatuses($statuses);
        }

        $statuses = self::addedStatusesFromLeadFLowIfNotExist($leadFlows, $statuses);
        $statuses = self::updateStatuses($statuses, $reasons, $leadFlows);
        ksort($statuses);

        if ($debug) {
            echo 'Reasons: ' . PHP_EOL;
            echo self::renderReasons($reasons);
            echo 'Lead flow: ' . PHP_EOL;
            echo self::renderLeadFlow($leadFlows);
            echo 'Status after: ' . PHP_EOL;
            echo self::renderStatuses($statuses);
        }

        $statuses = self::updateEmpty($statuses);
        $statuses = self::recalculateDuration($statuses);

        if ($debug) {
            echo '<br> Status new: ' . PHP_EOL;
            echo self::renderStatuses($statuses);
        }

        return self::updateLeadFlows($leadFlows, $statuses, $lead->id);
    }

    /**
     * @param array $statuses
     * @return array
     */
    public static function recalculateDuration(array $statuses): array
    {
        /** @var StatusLog $previous */
        $previous = null;
        /** @var StatusLog $status */
        foreach ($statuses as $key => $status) {
            if ($previous) {
                $previous->endDate = $status->date;
                $previous->duration = (strtotime($previous->endDate) - strtotime($previous->date));
            }
            $previous = $status;
        }
        return $statuses;
    }


    /**
     * @param $leadFlows
     * @param $statuses
     * @param int $leadId
     * @return array
     */
    public static function updateLeadFlows($leadFlows, $statuses, int $leadId): array
    {
        $report = [];
        /** @var LeadFlow $leadFlow */
        foreach ($leadFlows as $key => $leadFlow) {
            if (!isset($statuses[$key])) {
                try {
                    $leadFlow->delete();
                    unset($leadFlows[$key]);
                } catch (\Throwable $e) {
                    $report[] = 'Not deleted LeadFlow with Id:' . $leadFlow->id;
                }
                continue;
            }
            /** @var StatusLog $status */
            $status = $statuses[$key];
            if ($error = self::updateLeadFlow($leadFlow, $status)) {
                $report[] = $error;
            }
        }
        foreach ($statuses as $key => $status) {
            if (!isset($leadFlows[$key])) {
                 if ($error = self::insertLeadFLow($status, $leadId)) {
                    $report[] = $error;
                }
            }
        }

        return $report;
    }

    /**
     * @param StatusLog $status
     * @param $leadId
     * @return string
     */
    public static function insertLeadFLow(StatusLog $status, $leadId): string
    {
        $leadFlow = new LeadFlow();
        $leadFlow->created = $status->date;
        $leadFlow->employee_id = $status->createdUserId;
        $leadFlow->lf_owner_id = $status->newOwner;
        $leadFlow->lead_id = $leadId;
        $leadFlow->status = $status->newStatus;
        $leadFlow->lf_from_status_id = $status->oldStatus;
        $leadFlow->lf_end_dt = $status->endDate;
        $leadFlow->lf_time_duration = $status->duration;
        $leadFlow->lf_description = $status->description;
        if (!$leadFlow->save()) {
            return 'Not inserted LeadFlow for LeadId: ' . $leadId . ' with created date: ' . $status->date;
        }
        return '';
    }

    /**
     * @param LeadFlow $leadFlow
     * @param StatusLog $status
     * @return string
     */
    public static function updateLeadFlow(LeadFlow $leadFlow, $status): string
    {
        if (
        $leadFlow->employee_id != $status->createdUserId
        || $leadFlow->lf_owner_id != $status->newOwner
        || $leadFlow->status != $status->newStatus
        || $leadFlow->lf_from_status_id != $status->oldStatus
        || $leadFlow->lf_end_dt != $status->endDate
        || $leadFlow->lf_time_duration != $status->duration
        || $leadFlow->lf_description != $status->description
    ) {
            $leadFlow->employee_id = $status->createdUserId;
            $leadFlow->lf_owner_id = $status->newOwner;
            $leadFlow->status = $status->newStatus;
            $leadFlow->lf_from_status_id = $status->oldStatus;
            $leadFlow->lf_end_dt = $status->endDate;
            $leadFlow->lf_time_duration = $status->duration;
            $leadFlow->lf_description = $status->description;
            if (!$leadFlow->save()) {
                return 'Not updated LeadFlow with Id: ' . $leadFlow->id;
            }
        }
        return '';
    }

    public static function updateStatuses($statuses, $reasons, $leadFlows)
    {
        /** @var StatusLog $status */
        foreach ($statuses as $status) {
            if (isset($leadFlows[$status->date])) {
                self::updateInfoFromLeadFlow($status, $leadFlows[$status->date]);
            }
            if (isset($reasons[$status->date])) {
                self::updateInfoFromReasons($status, $reasons[$status->date]);
            }
        }
        return $statuses;
    }

    /**
     * @param StatusLog $status
     * @param $leadFlow
     */
    public static function updateInfoFromLeadFlow(StatusLog $status, $leadFlow): void
    {
        $status->createdUserId = $leadFlow['employee_id'];
        $status->description = $leadFlow['lf_description'] ?: $status->description;
        if (!$status->oldStatus) {
            $status->oldStatus = $leadFlow['lf_from_status_id'];
        }
        if (!$status->newStatus) {
            $status->newStatus = $leadFlow['status'];
        }
    }

    /**
     * @param StatusLog $status
     * @param $reason
     */
    public static function updateInfoFromReasons(StatusLog $status, $reason): void
    {
        $status->createdUserId = $reason['employee_id'] ?: $status->createdUserId;
        $status->description = $reason['reason'] ?: $status->description;
    }

    public static function createStatuses($logs): array
    {
        $statuses = [];
        foreach ($logs as $log) {
            $data = Json::decode($log['message']);
            if (isset($data['model']) && $data['model'] === 'Lead') {
                if (isset($data['oldParams']) && isset($data['newParams'])) {
                    if (
                        isset($data['oldParams']['status'])
                        || isset($data['newParams']['status'])
                        || isset($data['oldParams']['employee_id'])
                        || isset($data['newParams']['employee_id'])
                    ) {
                        $statuses[$log['created']] = new StatusLog(
                            $data['oldParams']['status'] ?? null,
                            $data['newParams']['status'] ?? null,
                            $data['oldParams']['employee_id'] ?? null,
                            $data['newParams']['employee_id'] ?? null,
                            $log['created']
                        );
                    }
                }
            }
        }

        return $statuses;
    }

    /**
     * @param $leadFlows
     * @param array $statuses
     * @return array
     */
    public static function addedStatusesFromLeadFLowIfNotExist($leadFlows, array $statuses): array
    {
        foreach ($leadFlows as $key => $leadFlow) {
            if (!isset($statuses[$key])) {
                $statuses[$key] = new StatusLog(
                    $leadFlow['lf_from_status_id'] ?? null,
                    $leadFlow['status'] ?? null,
                    null,
                    $leadFlow['lf_owner_id'] ?? null,
                    $leadFlow['created'],
                    $leadFlow['employee_id']
                );
            }
        }
        return $statuses;
    }

    /**
     * @param array $statuses
     * @return array
     */
    public static function updateEmpty(array $statuses): array
    {

        $previous = new StatusLog(null, null, null, null, null, null);
        $previousKey = null;

        /** @var StatusLog $current */
        foreach ($statuses as $key => $current) {
            if (!$current->oldStatus && isset($previous->newStatus)) {
                $current->oldStatus = $previous->newStatus;
            }
            /**
             * Set current statuses if is empty.
             */
            if (!$current->oldStatus && !$current->newStatus) {
                $current->oldStatus = $previous->newStatus;
                $current->newStatus = $previous->newStatus;
            }

            /**
             * Set current owners if is empty
             */
            if (!$current->oldOwner && !$current->newOwner) {
                $current->oldOwner = $previous->newOwner;
                $current->newOwner = $previous->newOwner;
            }

            /**
             * If rows the same and created date not more 3 seconds
             */
            if (
                $current->oldStatus == $previous->oldStatus
                && $current->newStatus == $previous->newStatus
                && $current->oldOwner == $previous->oldOwner
                && $current->newOwner == $previous->newOwner
                && $current->createdUserId == $previous->createdUserId
                && $current->description == $previous->description
            ) {
                $prevDate = (int)strtotime($previous->date);
                $currDate = (int)strtotime($current->date);
                if (($prevDate + 3) > $currDate) {
                    unset($statuses[$previousKey]);
                }
            } elseif (
                $current->oldStatus == $previous->oldStatus
                && $current->newStatus == $previous->newStatus
                && $current->description == $previous->description
            ) {
                $prevDate = (int)strtotime($previous->date);
                $currDate = (int)strtotime($current->date);
                if (($prevDate + 3) > $currDate) {
                    if (!$current->oldOwner) {
                        $current->oldOwner = $previous->oldOwner;
                    }
                    if (!$current->newOwner) {
                        $current->newOwner = $previous->newOwner;
                    }
                    if (!$current->createdUserId) {
                        $current->createdUserId = $previous->createdUserId;
                    }
                    unset($statuses[$previousKey]);
                }
            }

            $previous->oldStatus = $current->oldStatus;
            $previous->newStatus = $current->newStatus;
            $previous->oldOwner = $current->oldOwner;
            $previous->newOwner = $current->newOwner;
            $previous->createdUserId = $current->createdUserId;
            $previous->description = $current->description;
            $previous->date = $current->date;
            $previousKey = $key;

        }
        return $statuses;
    }

    /**
     * @param array $statuses
     * @return string
     */
    public static function renderStatuses(array $statuses): string
    {
        $out = '<table border="2" cellpadding="5">
                <tr>
                    <td>#</td>
                    <td>Old status</td>
                    <td>New status</td>
                    <td>Old owner</td>
                    <td>New owner</td>
                    <td>Created User</td>
                    <td>Description</td>
                    <td>Created</td>
                    <td>Ended</td>
                    <td>Duration</td>
                </tr>';
        $counter = 0;
        /** @var StatusLog $status */
        foreach ($statuses as $status) {
            $counter++;
            $out .= '<tr>';
            $out .= '<td align="center">' . $counter . '</td>';
            $out .= '<td align="center">' . $status->oldStatus . '</td>';
            $out .= '<td align="center">' . $status->newStatus . '</td>';
            $out .= '<td align="center">' . $status->oldOwner . '</td>';
            $out .= '<td align="center">' . $status->newOwner . '</td>';
            $out .= '<td align="center">' . $status->createdUserId . '</td>';
            $out .= '<td align="center">' . $status->description . '</td>';
            $out .= '<td align="center">' . $status->date . '</td>';
            $out .= '<td align="center">' . $status->endDate . '</td>';
            $out .= '<td align="center">' . $status->duration . '</td>';
            $out .= '</tr>';
        }
        $out .= '</table>';
        return $out;
    }

    /**
     * @param array $flows
     * @return string
     */
    public static function renderLeadFlow(array $flows): string
    {
        $out = '<table border="2" cellpadding="5">
                <tr>
                    <td>#</td>
                    <td>Employee Id</td>
                    <td>Owner</td>
                    <td>Old status</td>
                    <td>New status</td>
                    <td>Description</td>
                    <td>Created</td>                  
                </tr>';
        $counter = 0;
        foreach ($flows as $flow) {
            $counter++;
            $out .= '<tr>';
            $out .= '<td align="center">' . $counter . '</td>';
            $out .= '<td align="center">' . $flow['employee_id'] . '</td>';
            $out .= '<td align="center">' . $flow['lf_owner_id'] . '</td>';
            $out .= '<td align="center">' . $flow['lf_from_status_id'] . '</td>';
            $out .= '<td align="center">' . $flow['status'] . '</td>';
            $out .= '<td align="center">' . $flow['lf_description'] . '</td>';
            $out .= '<td align="center">' . $flow['created'] . '</td>';
            $out .= '</tr>';
        }
        $out .= '</table>';
        return $out;
    }

    /**
     * @param array $reasons
     * @return string
     */
    public static function renderReasons(array $reasons): string
    {
        $out = '<table border="2" cellpadding="5">
                <tr>
                    <td>#</td>
                    <td>Employee Id</td>
                    <td>Reason</td>
                    <td>Created</td>                  
                </tr>';
        $counter = 0;
        foreach ($reasons as $reason) {
            $counter++;
            $out .= '<tr>';
            $out .= '<td align="center">' . $counter . '</td>';
            $out .= '<td align="center">' . $reason['employee_id'] . '</td>';
            $out .= '<td align="center">' . $reason['reason'] . '</td>';
            $out .= '<td align="center">' . $reason['created'] . '</td>';
            $out .= '</tr>';
        }
        $out .= '</table>';
        return $out;
    }
}
