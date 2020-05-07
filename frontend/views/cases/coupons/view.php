<?php

use sales\entities\cases\Cases;
use yii\helpers\Url;
use yii\web\View;

/** @var $coupons \sales\model\coupon\entity\couponCase\CouponCase[] */
/** @var View $this */
/** @var Cases $model */

$urlRequestCoupons = Url::to(['/coupon/request', 'caseId' => $model->cs_id])

?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-case-coupons', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel">
	<div class="x_title" >
		<h2><i class="fa fa-sticky-note-o"></i> Coupons </h2>
		<ul class="nav navbar-right panel_toolbox">
            <li>
                <?= \yii\bootstrap\Html::a('<i class="fa fa-plus-circle success"></i> Request', '#', ['id' => 'btn-request-coupons', 'title' => 'Request coupons'])?>
            </li>
			<li>
				<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
	<div class="x_content" style="display: none; margin-top: -10px;">
        <br>
        <?php if ($coupons): ?>
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-case-coupons-table', 'enablePushState' => false, 'timeout' => 10000]) ?>
            <table class="table table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th style="width: 30px"> </th>
                    <th class="text-center" style="width: 130px">Code</th>
                    <th class="text-center" style="width: 130px">Amount</th>
                    <th class="text-center" style="width: 130px">Currency Code</th>
                    <th class="text-center" style="width: 130px">Percent</th>
                    <th class="text-center" style="width: 130px">Exp Date</th>
                    <th class="text-center" style="width: 130px">Start Date</th>
                    <th class="text-center" style="width: 130px">Status</th>
                </tr>
                </thead>
                <tbody>

                        <?php foreach($coupons as $key => $coupon): ?>
                            <tr>
                                <td><?= $key+1 ?></td>
                                <td><?= \yii\helpers\Html::checkbox($coupon->formName() . '[cc_case_id]['.$key.']', false, ['value' => $coupon->cc_coupon_id]) ?></td>
                                <td><?= $coupon->coupon->c_code ?></td>
                                <td><?= $coupon->coupon->c_amount ?></td>
                                <td><?= $coupon->coupon->c_currency_code ?></td>
                                <td><?= $coupon->coupon->c_percent ?></td>
                                <td><?= $coupon->coupon->c_exp_date ?></td>
                                <td><?= $coupon->coupon->c_start_date ?></td>
                                <td><?= $coupon->coupon->c_status_id ?></td>
                            </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-5">
                    <?= \yii\helpers\Html::dropDownList('email_tpl_key', null, \common\models\EmailTemplateType::getKeyList(false, null), ['prompt' => '---', 'class' => 'form-control', 'id' => 'email_tpl_key']) ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <?= \yii\helpers\Html::button('<i class="fa fa-envelope"></i> Send Coupons', ['id' => 'send-case-coupons', 'class' => 'btn btn-success']) ?>
                </div>
            </div>
		<?php \yii\widgets\Pjax::end()?>
        <?php else: ?>
            <p>Not found coupons</p>
        <?php endif; ?>
    </div>
</div>
<?php \yii\widgets\Pjax::end()?>

<?php
$js = <<<JS
    $(document).on('click', '#send-case-coupons', function () {
        
    });

    $('body').on('click', '#btn-request-coupons', function(e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');
        
        let modal = $('#modal-sm');
        $('#modal-sm-label').html('Request coupons');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load('{$urlRequestCoupons}' , function(response, status, xhr ) {
                    
            $('#preloader').addClass('d-none');
                        
            if (status == 'error') {                
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            } 
        });  
        return false;
    });
    
JS;

$this->registerJs($js);

