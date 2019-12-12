<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel AirportForm
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use frontend\models\search\AirportForm;
use common\models\Airport;

$template = <<<HTML
<div class="pagination-container row" style="margin-bottom: 10px;">
    <div class="col-sm-4" style="/*padding-top: 20px;*/">
        {summary}
    </div>
    <div class="col-sm-8" style="text-align: right;">
       {pager}
    </div>
</div>
<div class="table-responsive">
    {items}
</div>
HTML;

?>

<div class="card card-default">
    <div class="card-header">Airports</div>
    <div class="card-body">
        <?php if (Yii::$app->user->identity->canRole('admin')) : ?>
            <div class="mb-20">
                <?= Html::a('Sync Airports', '#', [
                    'class' => 'btn-success btn sync',
                    'data-url' => Url::to([
                        'settings/sync',
                        'type' => 'airports'
                    ])
                ]) ?>
            </div>
        <?php endif; ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'layout' => $template,
            'filterModel' => $searchModel,
            'columns' => [
                'iata',
                'name',
                'city',
                'country',
                'dst'
            ]
        ])
        ?>
    </div>
</div>