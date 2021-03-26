<?php

use modules\order\src\entities\order\Order;
use sales\auth\Auth;
use yii\widgets\Pjax;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var Order $order */
?>

<div class="order-view-invoice-box">
    <?php Pjax::begin(['id' => 'pjax-order-additional-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>

        <div class="x_panel x_panel_additional">
            <div class="x_title">
                <h2><i class="fas fa-info"></i> Additional</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <div class="x_panel">
                    <div class="x_title"></div>
                    <div class="x_content" style="display: block">
                        <?php if ($order->orLead && Auth::can('lead/view', ['lead' => $order->orLead])) : ?>
                            <?php echo Html::a('<i class="glyphicon glyphicon-search"></i> View Lead', [
                                '/lead/view/' . $order->orLead->gid,
                            ], [
                                'class' => 'btn btn-info btn-xs',
                                'target' => '_blank',
                                'data-pjax' => 0,
                                'title' => 'View',
                            ]); ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>

    <?php Pjax::end() ?>
</div>

<?php
$css = <<<CSS
    .x_panel_additional {
        background-color: #cad7e4;
    }
CSS;
$this->registerCss($css);
