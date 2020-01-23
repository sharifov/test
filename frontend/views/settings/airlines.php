<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel AirlineForm
 */

use common\models\Employee;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use frontend\models\search\AirlineForm;
use common\models\Airline;

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

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>

<div class="card card-default">
    <div class="card-header">Airlines</div>
    <div class="card-body">
        <?php if ($user->isAdmin()) : ?>
            <div class="mb-20">
                <?= Html::a('Sync Airlines', '#', [
                    'class' => 'btn-success btn sync',
                    'data-url' => Url::to([
                        'settings/sync',
                        'type' => 'airlines'
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
                'countryCode',
                'country',
            ]
        ])
        ?>
    </div>
</div>