<?php

namespace sales\yii\grid;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\grid\DataColumn;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class UserSelect2Column
 *
 * @property int $userId
 * @property string $url
 * @property string $relation
 * @property int $minimumInputLength
 * @property int $delay
 * @property string $placeholder
 * @property array $data
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
    public $delay = 300;
    public $placeholder = '';
    public $data = [];


    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if (empty($this->url)) {
            $this->url = Url::to(['employee/list-ajax']);
        }
        $model = $this->grid->filterModel;

        //VarDumper::dump($attr, 10 , true); exit;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            $userId = (int) $model->getAttribute($this->attribute);
            if ($userId) {
                $user = Employee::find()->select(['id', 'username'])->where(['id' => $userId])->cache(3600)->one();
                if ($user) {
                    $this->data[$user->id] = $user->username . ' ('.$user->id.')';
                }
            }
        }

    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string|null
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($user = $model->{$this->relation})) {
            /** @var Employee $user */
            return $user->username;
        }

        return null;
    }

    /**
     * @return array|false|string|null
     * @throws \Exception
     */
    protected function renderFilterCellContent()
    {

        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        $widgetOptions = [
            'model' => $model,
            'attribute' => $this->attribute,
            'data' => $this->data, //[$model->{$this->attribute} => 'asdasd'],
            'theme' => Select2::THEME_KRAJEE,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => $this->minimumInputLength,
                'ajax' => [
                    'url' => $this->url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    'delay' => $this->delay
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('formatText'),
                //'templateResult' => new JsExpression('function(city) { return city.text; }'),
                //'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
            ],
            'options' => ['placeholder' => $this->placeholder, 'class' => 'form-control'],
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

        return Select2::widget($widgetOptions);
    }
}
