<?php

use common\components\grid\DateColumn;
use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use src\model\user\entity\sales\SalesSearch;
use common\models\Lead;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var SalesSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
?>

<?php Pjax::begin(['id' => 'pjax-qualified-leads', 'timeout' => 5000, 'enablePushState' => true]); ?>

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
                'attribute' => 'status_id',
                'value' => static function ($data) {
                    return Lead::getStatus($data['status']);
                },
                'filter' => Lead::getStatusList(),
            ],
            [
                'attribute' => 'luc_description',
                'filter' =>  LeadUserConversionDictionary::DESCRIPTION_LIST,
            ],
            [
                'attribute' => 'luc_created_dt',
                'class' => DateColumn::class,
            ],
        ],
    ]) ?>

<?php Pjax::end();
