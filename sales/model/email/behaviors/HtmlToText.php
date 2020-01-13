<?php

namespace sales\model\email\behaviors;

use sales\helpers\email\TextConvertingHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class HtmlToText extends Behavior
{
    public $fromAttribute = 'e_email_body_blob';
    public $toAttribute = 'e_email_body_text';

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'clean',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'checkUpdate',
        ];
    }

    public function clean(): void
    {
        $this->owner->{$this->toAttribute} = TextConvertingHelper::unCompressAndHtmlToText($this->owner->{$this->fromAttribute});
    }

    public function checkUpdate(): void
    {
        if ($this->owner->isAttributeChanged($this->fromAttribute)) {
            $this->clean();
        }
    }
}