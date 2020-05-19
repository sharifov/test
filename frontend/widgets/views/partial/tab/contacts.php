<?php

use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;


/** @var View $this */
/** @var array $userPhones */
/** @var array $userEmails */

echo $this->render('@frontend/widgets/newWebPhone/view/sms', ['userPhones' => $userPhones]);
echo $this->render('@frontend/widgets/newWebPhone/view/email', ['userEmails' => $userEmails]);

?>

<div class="phone-widget__tab " id="tab-contacts">
    <div class="contacts__search-wrap">
        <label class="contacts__icon" for="">
            <i class="fa fa-search"></i>
        </label>
        <?php

        $form = ActiveForm::begin([
            'id' => 'contact-list-ajax',
            'action' => ['/contacts/search-list-ajax'],
            'method' => 'get',
        ]);

        echo Html::input('text', 'q', null, [
            'id' => 'contact-list-ajax-q',
            'class' => 'contacts__search-input',
            'placeholder' => 'Name, company, phone or email',
            'autocomplete' => 'off',
        ]);

        ActiveForm::end()

        ?>

    </div>

    <?php

    $titleAccessGetMessages = '';
    $disabledClass = '';
    $accessGetSms = Auth::can('/sms/list-ajax');
    if (!$accessGetSms) {
        $titleAccessGetMessages = 'Access denied';
        $disabledClass = '-disabled';
    }
    $urlFullList = Url::to(['/contacts/full-list-ajax']);

    $js = <<<JS
PhoneWidgetContacts.init('{$titleAccessGetMessages}', '{$disabledClass}', '{$urlFullList}');
JS;

    $this->registerJs($js);
    ?>

    <div class="contacts-list contacts-list--selection" id="list-of-contacts"></div>

</div>
