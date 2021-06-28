<?php

use common\components\grid\DateColumn;
use sales\model\leadUserConversion\service\LeadUserConversionDictionary;
use sales\model\user\entity\sales\SalesSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var SalesSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
?>

<?php Pjax::begin(['id' => 'pjax-qualified-leads', 'timeout' => 5000, 'enablePushState' => false]); ?>

<?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'label' => 'Lead',
                'value' => static function ($data) {
                    return Html::tag('i', '', ['class' => 'fa fa-arrow-right'])
                        . ' '
                        . Html::a(
                            'lead: ' . $data['id'],
                            ['/lead/view', 'gid' => $data['gid']],
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                }
            ],
            [
                'attribute' => 'luc_description',
                'filter' => LeadUserConversionDictionary::DESCRIPTION_LIST,
            ],
            [
                'attribute' => 'luc_created_dt',
                'class' => DateColumn::class,
            ],
        ],
    ]) ?>

<?php Pjax::end();
