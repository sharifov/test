<?php

namespace sales\widgets;

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class EmailSelect2Widget
 *
 * @property int $minimumInputLength
 * @property int $delay
 * @property string $placeholder
 * @property string $url
 *
 * Ex.
        <?= $form->field($model, 'upp_email_list_id')->widget(EmailSelect2Widget::class, [
            'data' => $model->upp_email_list_id ? [
                $model->upp_email_list_id => $model->emailList->el_email
            ] : [],
        ]) ?>
 */
class EmailSelect2Widget extends Select2
{
    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $url;

    public function init(): void
    {
        parent::init();

        $this->url = $this->url ?: Url::to(['/email-list/list-ajax']);
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
            'templateResult' => new JsExpression('formatEmailListText'),
            'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
        ], $this->pluginOptions);
        $this->options = ArrayHelper::merge(['placeholder' => $this->placeholder, 'class' => 'form-control'], $this->options);
    }

    public function registerAssets(): void
    {
        parent::registerAssets();
        $js = <<<JS
function formatEmailListText( data ) {
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
        $this->getView()->registerJs($js, View::POS_HEAD, '/email-list/list-ajax');
    }
}
