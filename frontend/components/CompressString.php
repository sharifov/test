<?php
namespace frontend\components;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class CompressString extends Behavior
{
    public $inAttribute = 'e_email_body_html';
    public $outAttribute = 'e_email_body_blob';
    public $level = 9;
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'compress',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'compress',
        ];
    }

    public function compress(): void
    {
        $this->owner->{$this->outAttribute} = gzcompress($this->owner->{$this->inAttribute}, $this->level);
    }
}