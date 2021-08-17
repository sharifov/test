<?php

namespace webapi\src\forms\boWebhook;

use common\models\Project;
use yii\base\Model;

/**
 * Class ReprotectionUpdateForm
 *
 * @property string $booking_id
 * @property string $project_key
 * @property string $reprotection_quote_gid
 */
class ReprotectionUpdateForm extends Model
{
    public $booking_id;
    public $project_key;
    public $reprotection_quote_gid;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id', 'reprotection_quote_gid'], 'string'],
            [['project_key'], 'string', 'max' => 50],
            ['project_key', 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['project_key' => 'project_key'], 'skipOnError' => true]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
