<?php
/* @var $this yii\web\View */
/* @var $model Order */
/* @var $index integer */

use common\models\Order;
use yii\bootstrap4\Html;

?>

<div class="x_panel">
    <div class="x_title">
        <h2>

            <?//= Html::checkbox('offer_checkbox', false, ['id' => 'off_ch' . $model->or_id, 'style' => 'width: 18px; height: 18px;'])?>

            <span class="badge badge-info">
                OR<?=($model->or_id)?>
            </span>
            "<?=\yii\helpers\Html::encode($model->or_name)?>"  (<?=\yii\helpers\Html::encode($model->or_uid)?>)
        </h2>
        <ul class="nav navbar-right panel_toolbox">
            <!--            <li>-->
            <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
            <!--            </li>-->
            <li>
                <?= Html::a('<i class="fa fa-edit warning"></i> Update order', null, [
                    'data-url' => \yii\helpers\Url::to(['/order/update-ajax', 'id' => $model->or_id]),
                    'class' => 'btn-update-order'
                ])?>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                <div class="dropdown-menu" role="menu">
                    <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete order', null, [
                        'class' => 'dropdown-item text-danger btn-delete-order',
                        'data-order-id' => $model->or_id,
                        'data-url' => \yii\helpers\Url::to(['order/delete-ajax']),
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->or_created_dt)) ?>
        <i class="fa fa-user"></i> <?=$model->orCreatedUser ? Html::encode($model->orCreatedUser->username) : '-'?>
    </div>
</div>