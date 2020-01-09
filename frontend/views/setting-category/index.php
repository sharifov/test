<?php

use common\models\SettingCategory;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Setting Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Setting Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'sc_id',
            'sc_name',
            'sc_enabled:booleanByLabel',
            'sc_created_dt:byUserDateTime',
            'sc_updated_dt:byUserDateTime',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
