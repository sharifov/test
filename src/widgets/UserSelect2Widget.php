<?php

namespace src\widgets;

use common\models\Employee;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class UserSelect2Widget
 *
 * @property int $minimumInputLength
 * @property int $delay
 * @property string $placeholder
 * @property string $url
 *
 * Ex.
    <?= $form->field($model, 'cl_user_id')->widget(UserSelect2Widget::class, [
        'data' => $model->cl_user_id ? [
            $model->cl_user_id => $model->user->username
        ] : [],
    ]) ?>
 */
class UserSelect2Widget extends Select2
{
    public $minimumInputLength = 1;
    public $delay = 300;
    public $placeholder = '';
    public $url;

    public function init(): void
    {
        parent::init();

        $this->url = $this->url ?: Url::to(['/employee/list-ajax']);
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
            'templateResult' => new JsExpression('formatUserText'),
            'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
        ], $this->pluginOptions);
        $this->options = ArrayHelper::merge(['placeholder' => $this->placeholder, 'class' => 'form-control'], $this->options);

        if (!empty($this->value)) {
            $userId = intval($this->value);
            $user = Employee::find()->select(['id', 'username'])->where(['id' => $userId])->cache(3600)->one();

            if ($user) {
                $this->data[$user->id] = $user->username;
            }
        }
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
        $this->getView()->registerJs($js, View::POS_HEAD, '/employee/list-ajax');
    }
}
