<?php

use modules\order\src\entities\order\Order;
use sales\auth\Auth;
use yii\widgets\Pjax;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var Order $order */

?>

    <div class="order-view-contacts-box">
        <?php Pjax::begin(['id' => 'pjax-order-additional-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>

        <div class="x_panel x_panel_contacts">
            <div class="x_title">
                <h2><i class="fas fa-address-book"></i> Contacts</h2>
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
                        <?php if ($order->orderContacts) : ?>
                            <div>
                                <?= \yii\grid\GridView::widget([
                                    'dataProvider' => new \yii\data\ArrayDataProvider([
                                        'allModels' => $order->orderContacts
                                    ]),
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],
                                        'oc_first_name',
                                        [
                                            'attribute' => 'oc_middle_name',
                                            'value' => static function ($model) {
                                                return $model['oc_middle_name'] ?? '-';
                                            }
                                        ],
                                        'oc_last_name',
                                        'oc_email',
                                        'oc_phone_number'
                                    ]
                                ]) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php Pjax::end() ?>
    </div>

<?php
$css = <<<CSS
    .x_panel_contacts {
        background-color: #dff2f2;
    }
CSS;
$this->registerCss($css);
