<?php

namespace sales\widgets;

use kartik\select2\Select2;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

class DepartmentSelect2Widget extends Select2
{
    private const SELECTION_NAME = 'dep_name';
    private const SELECTION_ID = 'dep_id';

    private const SELECTION_LIST = [
        self::SELECTION_NAME,
        self::SELECTION_ID,
    ];

    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $url;
    public $selection = self::SELECTION_ID;


    public function init(): void
    {
        parent::init();

        self::checkSelection($this->selection);

        $this->url = $this->url ?: Url::to(['/department/list-ajax']);
        $this->theme = $this->theme ?: self::THEME_KRAJEE;
        $this->pluginOptions = ArrayHelper::merge([
            'allowClear' => true,
            'minimumInputLength' => $this->minimumInputLength,
            'ajax' => [
                'url' => $this->url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term, selection: "' . $this->selection . '"}; }'),
                'delay' => $this->delay
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('formatUserText'),
            'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
        ], $this->pluginOptions);
        $this->options = ArrayHelper::merge(['placeholder' => $this->placeholder, 'class' => 'form-control'], $this->options);
    }

    public function registerAssets(): void
    {
        parent::registerAssets();
        $js = <<<JS
function formatUserText( data ) {
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
        $this->getView()->registerJs($js, View::POS_HEAD, '/department/list-ajax');
    }

    public static function checkSelection(string $selection): void
    {
        if (!in_array($selection, self::SELECTION_LIST)) {
            throw new InvalidArgumentException('Unknown provided selection');
        }
    }
}
