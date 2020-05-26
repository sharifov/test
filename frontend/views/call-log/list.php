<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $searchModel sales\model\callLog\entity\callLog\search\CallLogSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'My Calls Log';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="call-log-list">
    <h1><i class="fa fa-phone"></i> <?= Html::encode($this->title) ?></h1>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'cl_id'
    ]
]); ?>
