<?php

use frontend\models\search\ComposerLockSearch;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */
/* @var $searchModel ComposerLockSearch */

$this->title = 'Composer .lock Info';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="composer-info-page">
    <h1><i class="glyphicon glyphicon-list-alt"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-download"></i> Export All JSON data', ['export-composer-lock'], [
            'class' => 'btn btn-primary',
            'title' => 'Export .loc composer data',
            'data' => [
                'confirm' => 'Are you sure you want to export composer .loc data?'
            ],
        ]) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-composer-loc']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'attribute' => 'name',
                //'options' => ['style' => 'width: 70px']
            ],
            [
                'attribute' => 'type',
            ],
            [
                'attribute' => 'version',
            ],
            [
                'attribute' => 'license',
            ],
            [
                'attribute' => 'source',
            ],
             [
                'attribute' => 'autors',
            ],
             [
                'attribute' => 'comments',
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>

    <?php /*if ($tables) : ?>
        <table class="table table-bordered table-hover table-striped">
            <tr>
                <th>Nr</th>
                <th>Name</th>
                <th>Engine</th>
                <th>Table Rows</th>
                <th>Data Length</th>
                <th>Index Length</th>
                <th>Auto increment</th>
                <th>Create time</th>
                <th>Table Collation</th>
            </tr>
        <?php foreach ($tables as $n => $table) : ?>
            <tr>
                <td><?= ($n + 1) ?></td>
                <td><b><?=Html::encode($table['TABLE_NAME'])?></b></td>
                <td><?=Html::encode($table['ENGINE'])?></td>
                <td><?=Html::encode($table['TABLE_ROWS'])?></td>
                <td><?=Html::encode($table['DATA_LENGTH'])?></td>
                <td><?=Html::encode($table['INDEX_LENGTH'])?></td>
                <td><?=Html::encode($table['AUTO_INCREMENT'])?></td>
                <td><?=Html::encode($table['CREATE_TIME'])?></td>
                <td><?= $table['TABLE_COLLATION'] === 'utf8mb4_unicode_ci' ? Html::encode($table['TABLE_COLLATION']) : '<span class="danger">' . Html::encode($table['TABLE_COLLATION']) . '</span>'?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif;*/ ?>
</div>