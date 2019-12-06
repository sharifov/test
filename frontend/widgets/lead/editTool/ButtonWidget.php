<?php

namespace frontend\widgets\lead\editTool;

/**
 * Class ButtonWidget
 *
 * @property $modalId
 * @property Url $url
 * @property string $script
 */
class ButtonWidget extends \yii\base\Widget
{
    public $modalId;
    public $url;

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('button', [
            'modalId' => $this->modalId,
            'url' => $this->url
        ]);
    }
}