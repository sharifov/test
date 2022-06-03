<?php

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use yii\db\Migration;

/**
 * Class m220603_084648_move_data_shift_schedule_request_to_history
 */
class m220603_084648_move_data_shift_schedule_request_to_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $requestList = ShiftScheduleRequest::find()
            ->orderBy(['ssr_id' => SORT_ASC, 'ssr_uss_id' => SORT_ASC]);
        $parentRequest = null;
        $preventRequest = null;

        $this->execute("SET foreign_key_checks = 0;");
        foreach ($requestList->batch() as $requestBatch) {
            $values = [];
            foreach ($requestBatch as $request) {
                if (empty($parentRequest) || $parentRequest->ssr_uss_id !== $request->ssr_uss_id) {
                    $parentRequest = $request;
                    $preventRequest = $request;
                    $values[] = [
                        $parentRequest->ssr_id,
                        null,
                        $parentRequest->ssr_status_id,
                        null,
                        $parentRequest->ssr_description,
                        $parentRequest->ssr_created_dt,
                        null,
                        $parentRequest->ssr_created_user_id,
                        null,
                    ];
                } else {
                    $values[] = [
                        $preventRequest->ssr_id,
                        $preventRequest->ssr_status_id,
                        $request->ssr_status_id,
                        $preventRequest->ssr_description,
                        $request->ssr_description,
                        $preventRequest->ssr_created_dt,
                        null,
                        $request->ssr_updated_user_id ?: $parentRequest->ssr_created_user_id,
                        null,
                    ];
                    $preventRequest = $request;
                    // Update parent request
                    $parentRequest->ssr_status_id = $request->ssr_status_id;
                    $parentRequest->ssr_description = $request->ssr_description;
                    $parentRequest->save();
                    // Remove request
                    $request->delete();
                }
            }

            $this->batchInsert(
                '{{%shift_schedule_request_history}}',
                [
                    'ssrh_ssr_id',
                    'ssrh_from_status_id',
                    'ssrh_to_status_id',
                    'ssrh_from_description',
                    'ssrh_to_description',
                    'ssrh_created_dt',
                    'ssrh_updated_dt',
                    'ssrh_created_user_id',
                    'ssrh_updated_user_id',
                ],
                $values
            );
        }
        $this->execute("SET foreign_key_checks = 1;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%shift_schedule_request_history}}');
    }
}
