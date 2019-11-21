<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Hotel Product Module';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-default-index">
    <h1><i class="fa fa-hotel"></i> <?= Html::encode($this->title) ?></h1>
    <div class="row">
    <div class="col-lg-6">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Nr</th>
                <th>Name</th>
                <th>Link</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Hotel product list</td>
                <td><?=\yii\bootstrap4\Html::a('Go to link', ['product-list/index'], ['class' => 'btn btn-default'])?></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Hotel product quotes</td>
                <td><?=\yii\bootstrap4\Html::a('Go to link', ['product-list/index'], ['class' => 'btn btn-default'])?></td>
            </tr>
        </tbody>
    </table>
    </div>
    </div>
</div>
