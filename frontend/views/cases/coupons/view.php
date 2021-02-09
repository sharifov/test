<?php

use sales\entities\cases\Cases;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var $coupons \sales\model\coupon\entity\couponCase\CouponCase[] */
/** @var $sendCouponsForm \sales\model\coupon\useCase\send\SendCouponsForm */
/** @var View $this */
/** @var Cases $model */
/** @var \frontend\models\CaseCouponPreviewEmailForm $previewEmailForm */

if ($model) {
    $urlRequestCoupons = Url::to(['/coupon/request', 'caseId' => $model->cs_id]);
}

$urlSendCoupons = !isset($previewEmailForm) ? Url::to(['/coupon/preview']) : null;

$clientEmails = $model->client ? $model->client->getEmailList() : [];
?>

<script>
    if (typeof pjaxOffFormSubmit === 'function') {
        pjaxOffFormSubmit('#pjax-case-coupons-table');
    }
</script>
<div class="x_panel">
    <div class="x_title" >
        <h2><i class="fa fa-sticky-note-o"></i> Coupons </h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if ($model) : ?>
                <li>
                    <?= \yii\bootstrap\Html::a('<i class="fa fa-plus-circle success"></i> Generate coupons', '#', ['id' => 'btn-request-coupons', 'title' => 'Request coupons'])?>
                </li>
            <?php endif; ?>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="margin-top: -10px;">
        <div class="row">
            <div class="col-md-12">
            </div>
        </div>
        <br>
            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-case-coupons-table', 'enableReplaceState' => false, 'enablePushState' => false, 'timeout' => 10000]) ?>
            <?php if ($coupons) : ?>
                <?php if (isset($previewEmailForm)) : ?>
                    <?= $this->render('_email_preview', [
                        'previewEmailForm' => $previewEmailForm,
                        'case' => $model
                    ]) ?>
                <?php else : ?>
                    <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['data-pjax' => 1], 'id' => 'case_send_coupons', 'action' => [$urlSendCoupons]]); ?>
                    <?= $form->field($sendCouponsForm, 'caseId')->hiddenInput()->label(false) ?>
                    <?= $form->errorSummary($sendCouponsForm) ?>
                <table class="table table-bordered table-hover table-condensed">
                    <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th style="width: 30px"> </th>
                        <th class="text-center" style="width: 130px">Code</th>
                        <th class="text-center" style="width: 130px">Amount</th>
                        <th class="text-center" style="width: 130px">Currency Code</th>
                        <th class="text-center" style="width: 130px">Exp Date</th>
                        <th class="text-center" style="width: 130px">Created Date</th>
                        <th class="text-center" style="width: 130px">Status</th>
                    </tr>
                    </thead>
                    <tbody>

                            <?php foreach ($coupons as $key => $coupon) : ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td>
                                        <?php if (!$coupon->coupon->isSend()) : ?>
                                            <?= Html::checkbox($sendCouponsForm->formName() . '[couponIds][' . $key . ']', false, ['value' => $coupon->cc_coupon_id]) ?>
                                        <?php  endif; ?>
                                    </td>
                                    <td><?= $coupon->coupon->c_code ?></td>
                                    <td><?= $coupon->coupon->c_amount ?></td>
                                    <td><?= $coupon->coupon->c_currency_code ?></td>
                                    <td><?= $coupon->coupon->c_exp_date ?></td>
                                    <td><?= Yii::$app->formatter->asDatetime(strtotime($coupon->coupon->c_created_dt)) ?></td>
                                    <td><?= \sales\model\coupon\entity\coupon\CouponStatus::asFormat($coupon->coupon->c_status_id) ?></td>
                                </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="row">
<!--                    <div class="col-md-5">-->
                    <?= $form->field($sendCouponsForm, 'emailTemplateType')->hiddenInput(['value' => 'cl_coupon'])->label(false) ?>
<!--                        --><?php // $form->field($sendCouponsForm, 'emailTemplateType')->dropDownList(\common\models\EmailTemplateType::getKeyList(false, null), ['prompt' => '---', 'class' => 'form-control', 'id' => 'email_tpl_key'])?>

<!--                    </div>-->

                    <div class="col-md-5">
                        <?= $form->field($sendCouponsForm, 'emailTo')->dropDownList($clientEmails, ['prompt' => '---', 'class' => 'form-control']) ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <?= Html::submitButton('<i class="fa fa-envelope"></i> Preview Email', ['id' => 'preview-case-coupons', 'class' => 'btn btn-success']) ?>
                    </div>
                </div>
                    <?php \yii\widgets\ActiveForm::end() ?>
                <?php endif; ?>
            <?php else : ?>
                <p>Not found coupons</p>
            <?php endif; ?>
            <?php \yii\widgets\Pjax::end()?>
    </div>
</div>

<?php
if ($model) {
    $js = <<<JS
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
}
