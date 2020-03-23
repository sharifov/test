<?php

namespace sales\widgets;

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class PhoneSelect2Widget
 *
 * @property int $minimumInputLength
 * @property int $delay
 * @property string $placeholder
 * @property string $url
 */
class PhoneSelect2Widget extends Select2
{
    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $url;

    public function init(): void
    {
        parent::init();

        $this->url = $this->url ?: Url::to(['/phone-list/list-ajax']);
        $this->theme = $this->theme ?: self::THEME_KRAJEE;
        $this->pluginOptions = ArrayHelper::merge([
            'allowClear' => true,
            'minimumInputLength' => $this->minimumInputLength,
            'ajax' => [
                'url' => $this->url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                'delay' => $this->delay
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('formatPhoneListText'),
            'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
        ], $this->pluginOptions);
        $this->options = ArrayHelper::merge(['placeholder' => $this->placeholder, 'class' => 'form-control'], $this->options);
    }

    public function registerAssets(): void
    {
        parent::registerAssets();
        $js = <<<JS
function formatPhoneListText( data ) {
    if (data.loading) {
        return data.text;
    }
    let str = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + data.text + "</div>";
    str +=	"</div></div>";
    return str;
}
JS;
        $this->getView()->registerJs($js, View::POS_HEAD, '/phone-list/list-ajax');
    }
}
