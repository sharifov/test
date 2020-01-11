<?php

namespace sales\model\email\behaviors;

use sales\helpers\email\TextConvertingHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class CompressText extends Behavior
{
    public $fromAttribute = 'e_email_body_html';
    public $toAttribute = 'e_email_body_blob';

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'compress',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'checkUpdate',
        ];
    }

    public function compress(): void
    {
        $this->owner->{$this->toAttribute} = TextConvertingHelper::compress($this->owner->{$this->fromAttribute});
    }

    public function checkUpdate(): void
    {
        if ($this->owner->isAttributeChanged($this->fromAttribute)) {
            $this->compress();
        }
    }
}