<?php
namespace frontend\components;

use Soundasleep\Html2Text;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class EmailHtmlToText extends Behavior
{
    public $inAttribute = 'e_email_body_html';
    public $outAttribute = 'e_email_body_text';
    public $ignoreErrors = true;
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'clean',
        ];
    }

    public function clean(): void
    {
        $this->owner->{$this->outAttribute} = Html2Text::convert($this->owner->{$this->inAttribute}, ['ignore_errors' => $this->ignoreErrors]);
    }
}