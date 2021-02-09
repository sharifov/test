<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\userClientChatData\entity\UserClientChatData */

$this->title = $model->uccd_id;
$this->params['breadcrumbs'][] = ['label' => 'User Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-client-chat-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->uccd_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->uccd_id], [
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
                'uccd_id',
                'uccd_employee_id:username',
                'uccd_active:booleanByLabel',
                'uccd_created_dt:byUserDateTime',
                'uccd_updated_dt:byUserDateTime',
                'uccd_created_user_id:username',
                'uccd_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
