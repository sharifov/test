<?php

namespace sales\yii\grid;

use common\models\Employee;
use Yii;
use sales\access\ListsAccess;
use yii\grid\DataColumn;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class UserSelect2Column
 *
 * @property $userId
 * @property $url
 * @property $relation
 *
 * Ex.
    [
        'class' => \sales\yii\grid\UserColumn::class,
        'attribute' => 'ugs_updated_user_id',
        'relation' => 'updatedUser',
 *      'url' => 'employee/list-ajax',
    ],
 *
 */
class UserSelect2Column extends DataColumn
{
    public $format = 'userName';
    public $url;
    public $relation;
    public $minimumInputLength = 1;

    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if (empty($this->url)) {
            $this->url = \yii\helpers\Url::to(['employee/list-ajax']);
        }

//        if ($this->filter === null) {
//            if (!$this->userId) {
//                $this->userId = Yii::$app->user->id ?? null;
//            }
//            $this->filter = (new ListsAccess($this->userId))->getEmployees();
//        }
    }

    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($user = $model->{$this->relation})) {
            /** @var Employee $user */
            return $user->username;
        }

        return null;
    }

    protected function renderFilterCellContent()
    {

        if (is_string($this->filter)) {
            return $this->filter;
        }


        $model = $this->grid->filterModel;
        $widgetOptions1 = [
            'model' => $model,
            'attribute' => $this->attribute,
            //'data' => [$model->{$this->attribute} => 162],
            'theme' => Select2::THEME_KRAJEE,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => $this->minimumInputLength,
                'ajax' => [
                    'url' => $this->url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('formatText'),
                //'templateResult' => new JsExpression('function(city) { return city.text; }'),
                //'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
            ],
            'options' => ['placeholder' => ''],
        ];

$js = <<<JS
function formatText( data ) {
    if (data.loading) return data.text;

    let str = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + data.text + "</div>";
    str +=	"</div></div>";
    return str;
}
JS;
        Yii::$app->view->registerJs($js, View::POS_HEAD, 'employee/list-ajax');

        return Select2::widget($widgetOptions1);
    }
}
