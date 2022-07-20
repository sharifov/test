<?php

use common\components\grid\DateTimeColumn;
use src\model\clientData\entity\ClientData;
use src\model\clientDataKey\entity\ClientDataKey;
use src\model\clientDataKey\service\ClientDataKeyService;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\clientData\entity\ClientDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-data']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'cd_id',
            'cd_client_id:client',
            [
                'attribute' => 'cd_key_id',
                'value' => static function (ClientData $model) {
                    if (!$key = ClientDataKeyService::getKeyByIdCache((int) $model->cd_key_id, null)) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return Yii::$app->formatter->asLabel($key);
                },
                'filter' => ClientDataKeyService::getListCache(null),
                'format' => 'raw',
            ],
            'cd_field_value',
            'cd_field_value_ui',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cd_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
