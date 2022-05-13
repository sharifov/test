<?php

namespace modules\shiftSchedule\src\forms;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\base\Model;
use yii\helpers\VarDumper;

/**
 * This is the form class for table "shift_schedule_type".
 *
 * @property int $sst_id
 * @property string $sst_key
 * @property string $sst_name
 * @property string|null $sst_title
 * @property int $sst_enabled
 * @property int $sst_readonly
 * @property int $sst_subtype_id
 * @property string|null $sst_color
 * @property string|null $sst_icon_class
 * @property string|null $sst_css_class
 * @property string|null $sst_params_json
 * @property int|null $sst_sort_order
 *
 * @property array|null $sst_label_list
 * @property bool $isNewRecord
 *
 */
class ShiftScheduleTypeForm extends Model
{
    public $sst_id;
    public $sst_key;
    public $sst_name;
    public $sst_title;
    public $sst_enabled;
    public $sst_readonly;
    public $sst_subtype_id;
    public $sst_color;
    public $sst_icon_class;
    public $sst_css_class;
    public $sst_params_json;
    public $sst_sort_order;

    public $sst_label_list;
    public $isNewRecord = true;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sst_key', 'sst_name'], 'required'],
            [['sst_enabled', 'sst_readonly', 'sst_subtype_id', 'sst_sort_order'], 'integer'],
            [['sst_params_json', 'sst_label_list'], 'safe'],
            [['sst_key', 'sst_name', 'sst_icon_class', 'sst_css_class'], 'string', 'max' => 100],
            [['sst_title'], 'string', 'max' => 255],
            [['sst_color'], 'string', 'max' => 20],
            [['sst_key'], 'unique', 'on' => 'insert', 'targetClass' => ShiftScheduleType::class],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'sst_id' => 'ID',
            'sst_key' => 'Key',
            'sst_name' => 'Name',
            'sst_title' => 'Title',
            'sst_enabled' => 'Enabled',
            'sst_readonly' => 'Readonly',
            'sst_subtype_id' => 'Subtype',
            'sst_color' => 'Color',
            'sst_icon_class' => 'Icon Class',
            'sst_css_class' => 'CSS Class',
            'sst_params_json' => 'Params Json',
            'sst_sort_order' => 'Sort Order',
            'sst_label_list' => 'Label List',
        ];
    }
}
