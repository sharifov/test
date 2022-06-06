<?php

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\services\UserShiftScheduleAttributeFormatService;
use yii\db\Migration;

/**
 * Class m220603_084648_move_data_shift_schedule_request_to_log
 */
class m220603_084648_move_data_shift_schedule_request_to_log extends Migration
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
                    if (!empty($parentRequest)) {
                        $parentRequest::updateAll($parentRequest->attributes, ['ssr_id' => $parentRequest->ssr_id]);
                    }
                    $parentRequest = $request;
                    $preventRequest = $request;
                    $oldAttr = null;
                    $newAttr = json_encode($request->getCustomValueAttributes());
                    $formatAttributeService = \Yii::createObject(UserShiftScheduleAttributeFormatService::class);
                    $formattedAttr = $formatAttributeService->formatAttr($request::className(), $oldAttr, $newAttr);

                    $values[] = [
                        $parentRequest->ssr_id,
                        $oldAttr,
                        $newAttr,
                        $formattedAttr,
                        $parentRequest->ssr_created_dt,
                        null,
                        $parentRequest->ssr_created_user_id,
                        null,
                    ];
                } else {
                    $oldAttr = json_encode($preventRequest->getCustomValueAttributes());
                    $newAttr = json_encode($request->getCustomValueAttributes());
                    $formatAttributeService = \Yii::createObject(UserShiftScheduleAttributeFormatService::class);
                    $formattedAttr = $formatAttributeService->formatAttr($request::className(), $oldAttr, $newAttr);

                    $values[] = [
                        $parentRequest->ssr_id,
                        $oldAttr,
                        $newAttr,
                        $formattedAttr,
                        $preventRequest->ssr_created_dt,
                        null,
                        $request->ssr_updated_user_id ?: $parentRequest->ssr_created_user_id,
                        null,
                    ];
                    $preventRequest = $request;
                    // Update parent request
                    $parentRequest->ssr_status_id = $request->ssr_status_id;
                    $parentRequest->ssr_description = $request->ssr_description;
                    // Remove request
                    $request->delete();
                }
            }
            if (!empty($parentRequest)) {
                $parentRequest::updateAll($parentRequest->attributes, ['ssr_id' => $parentRequest->ssr_id]);
            }
            $this->batchInsert(
                '{{%shift_schedule_request_log}}',
                [
                    'ssrh_ssr_id',
                    'ssrh_old_attr',
                    'ssrh_new_attr',
                    'ssrh_formatted_attr',
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
        $this->truncateTable('{{%shift_schedule_request_log}}');
    }
}
