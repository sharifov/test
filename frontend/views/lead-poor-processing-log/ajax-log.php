<?php

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider  */
/* @var $model \src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogSearch */

use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>

<?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false])?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'lppl_id',
        [
            'attribute' => 'lppl_lppd_id',
            'value' => static function (LeadPoorProcessingLog $model): string {
                if ($model->lpplLppd) {
                    return Html::tag('span', Html::encode($model->lpplLppd->lppd_name), [
                        'data-toggle' => 'tooltip',
                        'title' => 'ID: ' . $model->lppl_lppd_id
                    ]);
                }
                return '-';
            },
            'label' => 'Rule Name',
            'format' => 'raw'
        ],
        [
            'attribute' => 'lppl_status',
            'value' => static function (LeadPoorProcessingLog $model): string {
                return $model->getStatusName();
            },
        ],
        'lppl_owner_id:username',
        'lppl_created_dt:byUserDateTime',
        'lppl_updated_dt:byUserDateTime',
        'lppl_updated_user_id:username',
    ]
]); ?>
<?php Pjax::end();




