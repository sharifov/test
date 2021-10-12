<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\userData\entity\UserData */

$this->title = $model->ud_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'ud_user_id' => $model->ud_user_id, 'ud_key' => $model->ud_key], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'ud_user_id' => $model->ud_user_id, 'ud_key' => $model->ud_key], [
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
                'ud_user_id:userNameWithId',
                'ud_key:userDataKey',
                'ud_value',
                'ud_updated_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
