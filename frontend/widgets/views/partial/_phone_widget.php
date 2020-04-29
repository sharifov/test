<?php

use yii\web\View;

/** @var $phoneFrom string */
/** @var View $this */

?>

<div class="phone-widget" style="margin-bottom: 30px">
	<?php if($phoneFrom): ?>
    <div class="phone-widget__header">
        <div class="phone-widget__heading">
            <span class="phone-widget__title">Calls</span>
            <a href="#" class="phone-widget__close">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z"
                            fill="white" />
                </svg>
            </a>
        </div>
        <ul class="phone-widget__header-actions">
            <li>
                <a href="#" data-toggle-tab="tab-phone" class="is_active">
                    <i class="fas fa-phone"></i>
                    <span>Call</span>
                </a>
            </li>
<!--            <li>-->
<!--                <a href="#" data-toggle-tab="tab-contacts" >-->
<!--                    <i class="far fa-address-book"></i>-->
<!--                    <span>Contacts</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="#" data-toggle-tab="tab-history">-->
<!--                    <i class="fas fa-file-invoice"></i>-->
<!--                    <span>history</span>-->
<!--                </a>-->
<!--            </li>-->
        </ul>
    </div>
    <div class="phone-widget__body">
        <?= $this->render('tab/call'); ?>
        <?= $this->render('tab/contacts'); ?>
        <?= $this->render('tab/history'); ?>
    </div>
	<?php else: ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong> WebCall token is empty.
        </div>
	<?php endif; ?>
</div>
