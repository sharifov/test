<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequestHistory;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shift_schedule_request_history".
 *
 * @property int $ssrh_id
 * @property int|null $ssrh_ssr_id
 * @property string|null $ssrh_old_attr
 * @property string|null $ssrh_new_attr
 * @property string|null $ssrh_formatted_attr
 * @property string|null $ssrh_created_dt
 * @property string|null $ssrh_updated_dt
 * @property int|null $ssrh_created_user_id
 * @property int|null $ssrh_updated_user_id
 *
 * @property Employee $whoCreated
 * @property ShiftScheduleRequest $scheduleRequest
 * @property Employee $whoUpdated
 */
class ShiftScheduleRequestHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'shift_schedule_request_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ssrh_ssr_id', 'ssrh_created_user_id', 'ssrh_updated_user_id'], 'integer'],
            [['ssrh_old_attr', 'ssrh_new_attr', 'ssrh_formatted_attr', 'ssrh_created_dt', 'ssrh_updated_dt'], 'safe'],
            [['ssrh_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ssrh_created_user_id' => 'id']],
            [['ssrh_ssr_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleRequest::class, 'targetAttribute' => ['ssrh_ssr_id' => 'ssr_id']],
            [['ssrh_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ssrh_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ssrh_id' => 'Ssrh ID',
            'ssrh_ssr_id' => 'Ssrh Ssr ID',
            'ssrh_old_attr' => 'Ssrh Old Attr',
            'ssrh_new_attr' => 'Ssrh New Attr',
            'ssrh_formatted_attr' => 'Ssrh Formatted Attr',
            'ssrh_created_dt' => 'Ssrh Created Dt',
            'ssrh_updated_dt' => 'Ssrh Updated Dt',
            'ssrh_created_user_id' => 'Ssrh Created User ID',
            'ssrh_updated_user_id' => 'Ssrh Updated User ID',
        ];
    }

    /**
     * Gets query for [[SsrhCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getWhoCreated(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ssrh_created_user_id']);
    }

    /**
     * Gets query for [[SsrhSsr]].
     *
     * @return ActiveQuery
     */
    public function getScheduleRequest(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleRequest::class, ['ssr_id' => 'ssrh_ssr_id']);
    }

    /**
     * Gets query for [[SsrhUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getWhoUpdated(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ssrh_updated_user_id']);
    }
}
