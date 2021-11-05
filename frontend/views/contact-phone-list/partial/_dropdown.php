<?php

use sales\model\call\abac\CallAbacObject;
use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneData\service\ContactPhoneDataHelper;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\model\contactPhoneList\service\ContactPhoneListService;

/* @var yii\web\View $this */
/* @var ContactPhoneList $model */

?>
<div class="dropdown">
    <button
        class="btn btn-success dropdown-toggle"
        type="button"
        id="dropdownMenuButton_<?php echo $model->cpl_id ?>"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false">
        <i class="fa fa-check-square" id="dropdownIco_<?php echo $model->cpl_id ?>"></i>
    </button>
    <div role="menu" class="dropdown-menu">
        <?php /** @abac CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key allow_list */ ?>
        <?php if (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA)) : ?>
            <?php $name = ContactPhoneDataHelper::getName(ContactPhoneDataDictionary::KEY_ALLOW_LIST);
                $title = ContactPhoneListService::isAllowList($model->cpl_phone_number) ?
                '<i class="fa fa-times text-danger"></i>&nbsp;&nbsp;Remove from ' . $name :
                '<i class="fa fa-plus text-success"></i>&nbsp;&nbsp;Add to ' . $name; ?>
            <a
                class="dropdown-item js-toggle-data"
                data-model-id="<?php echo $model->cpl_id ?>"
                data-key="<?php echo ContactPhoneDataDictionary::KEY_ALLOW_LIST ?>"
                href="#"><?php echo $title ?></a>
        <?php endif ?>

        <?php /** @abac CallAbacObject::ACT_DATA_IS_TRUSTED, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key is_trusted */ ?>
        <?php if (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_IS_TRUSTED, CallAbacObject::ACTION_TOGGLE_DATA)) : ?>
            <?php $name = ContactPhoneDataHelper::getName(ContactPhoneDataDictionary::KEY_IS_TRUSTED);
                $title = ContactPhoneListService::isTrust($model->cpl_phone_number) ?
                '<i class="fa fa-times text-danger"></i>&nbsp;&nbsp;Remove from ' . $name :
                '<i class="fa fa-plus text-success"></i>&nbsp;&nbsp;Add to ' . $name; ?>
            <a
                class="dropdown-item js-toggle-data"
                data-model-id="<?php echo $model->cpl_id ?>"
                data-key="<?php echo ContactPhoneDataDictionary::KEY_IS_TRUSTED ?>"
                href="#"><?php echo $title ?></a>
        <?php endif ?>

        <?php /** @abac CallAbacObject::ACT_DATA_AUTO_CREATE_CASE_OFF, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key auto_create_case_off */ ?>
        <?php if (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_AUTO_CREATE_CASE_OFF, CallAbacObject::ACTION_TOGGLE_DATA)) : ?>
            <?php $name = ContactPhoneDataHelper::getName(ContactPhoneDataDictionary::KEY_AUTO_CREATE_CASE_OFF);
                $title = ContactPhoneListService::isAutoCreateCaseOff($model->cpl_phone_number) ?
                '<i class="fa fa-times text-danger"></i>&nbsp;&nbsp;Remove from ' . $name :
                '<i class="fa fa-plus text-success"></i>&nbsp;&nbsp;Add to ' . $name; ?>
            <a
                class="dropdown-item js-toggle-data"
                data-model-id="<?php echo $model->cpl_id ?>"
                data-key="<?php echo ContactPhoneDataDictionary::KEY_AUTO_CREATE_CASE_OFF ?>"
                href="#"><?php echo $title ?></a>
        <?php endif ?>

        <?php /** @abac CallAbacObject::ACT_DATA_AUTO_CREATE_LEAD_OFF, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key auto_create_lead_off */ ?>
        <?php if (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_AUTO_CREATE_LEAD_OFF, CallAbacObject::ACTION_TOGGLE_DATA)) : ?>
            <?php $name = ContactPhoneDataHelper::getName(ContactPhoneDataDictionary::KEY_AUTO_CREATE_LEAD_OFF);
                $title = ContactPhoneListService::isAutoCreateLeadOff($model->cpl_phone_number) ?
                '<i class="fa fa-times text-danger"></i>&nbsp;&nbsp;Remove from ' . $name :
                '<i class="fa fa-plus text-success"></i>&nbsp;&nbsp;Add to ' . $name; ?>
            <a
                class="dropdown-item js-toggle-data"
                data-model-id="<?php echo $model->cpl_id ?>"
                data-key="<?php echo ContactPhoneDataDictionary::KEY_AUTO_CREATE_LEAD_OFF ?>"
                href="#"><?php echo $title ?></a>
        <?php endif ?>

        <?php /** @abac CallAbacObject::ACT_DATA_INVALID, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key invalid */ ?>
        <?php if (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_INVALID, CallAbacObject::ACTION_TOGGLE_DATA)) : ?>
            <?php $name = ContactPhoneDataHelper::getName(ContactPhoneDataDictionary::KEY_INVALID);
                $title = ContactPhoneListService::isInvalid($model->cpl_phone_number) ?
                '<i class="fa fa-times text-danger"></i>&nbsp;&nbsp;Remove from ' . $name :
                '<i class="fa fa-plus text-success"></i>&nbsp;&nbsp;Add to ' . $name; ?>
            <a
                class="dropdown-item js-toggle-data"
                data-model-id="<?php echo $model->cpl_id ?>"
                data-key="<?php echo ContactPhoneDataDictionary::KEY_INVALID ?>"
                href="#"><?php echo $title ?></a>
        <?php endif ?>
    </div>
</div>
