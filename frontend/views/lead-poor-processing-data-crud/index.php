<?php

use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Poor Processing Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-poor-processing-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-lead-poor-processing-data']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'lppd_id',
            'lppd_enabled:boolean',
            'lppd_key',
            'lppd_name',
            'lppd_description',
            'lppd_minute',
            //'lppd_params_json',
            //'lppd_updated_dt',
            //'lppd_updated_user_id',

            ['class' => ActionColumn::class, 'template' => '{view} {update}'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
