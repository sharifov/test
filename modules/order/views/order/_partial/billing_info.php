<?php

use modules\order\src\entities\order\Order;
use sales\helpers\attribute\AttributeHelper;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var Order $order */
?>

<div class="order-view-invoice-box">
    <?php Pjax::begin(['id' => 'pjax-order-billing-info-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>

        <div class="x_panel x_panel_billing_info">
            <div class="x_title">
                <h2><i class="fas fa-file-invoice"></i> Billing Info</h2>
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

                        <table class="table table-bordered">
                            <?php if ($order->billingInfo) : ?>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                </tr>
                                <?php if ($order->billingInfo) :?>
                                    <?php foreach ($order->billingInfo as $info) : ?>
                                    <tr>
                                        <td>
                                            <?php echo AttributeHelper::showField($info, 'bi_first_name') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_last_name') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_middle_name') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_company_name') ?>
                                        </td>
                                        <td>
                                            <?php echo AttributeHelper::showField($info, 'bi_address_line1') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_address_line2') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_city') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_state') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_country') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_zip') ?>
                                        </td>
                                        <td>
                                            <?php echo AttributeHelper::showField($info, 'bi_contact_phone') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_contact_email') ?><br />
                                            <?php echo AttributeHelper::showField($info, 'bi_contact_name') ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php Pjax::end() ?>
</div>

<?php
$css = <<<CSS
    .x_panel_billing_info {
        background-color: #daf2e7;
    }
CSS;
$this->registerCss($css);
