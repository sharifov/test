<?php

use sales\model\userAuthClient\entity\UserAuthClient;
use sales\model\userAuthClient\entity\UserAuthClientSources;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\userAuthClient\entity\UserAuthClient */

$this->title = $model->uac_id;
$this->params['breadcrumbs'][] = ['label' => 'Auth Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'uac_id' => $model->uac_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'uac_id' => $model->uac_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uac_id',
            'uac_user_id:username',
            [
                'attribute' => 'uac_source',
                'value' => static function (UserAuthClient $model) {
                    return UserAuthClientSources::getName($model->uac_source);
                },
                'filter' => UserAuthClientSources::getList()
            ],
            'uac_source_id',
            'uac_email:email',
            'uac_ip',
            'uac_useragent',
            'uac_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
