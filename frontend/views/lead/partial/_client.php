<?php
use yii\widgets\ActiveForm;
use frontend\models\LeadForm;
use common\models\ClientEmail;
use common\models\ClientPhone;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $formClient ActiveForm
 * @var $leadForm LeadForm
 * @var $nr integer
 * @var $newPhone ClientPhone
 */

$formId = sprintf('%s-form', $leadForm->getClient()->formName());
?>

    <script>
        // add email comment button
        function addEmailComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addEmailComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
        // add email comment button
        function addPhoneComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addPhoneComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
    </script>

<?php $formClient = ActiveForm::begin([
    'enableClientValidation' => false,
    'id' => $formId
]); ?>
    <div class="sidebar__section">
        <h3 class="sidebar__subtitle">
            <i class="fa fa-user"></i>
        </h3>
        <?= $formClient->field($leadForm->getClient(), 'first_name')
            ->textInput([
                'class' => 'form-control lead-form-input-element'
            ]) ?>
        <?= $formClient->field($leadForm->getClient(), 'middle_name')
            ->textInput([
                'class' => 'form-control lead-form-input-element'
            ]) ?>
        <?= $formClient->field($leadForm->getClient(), 'last_name')
            ->textInput([
                'class' => 'form-control lead-form-input-element'
            ]) ?>
    </div>

    <div class="sidebar__section">
        <h3 class="sidebar__subtitle">
            <i class="fa fa-envelope"></i>
        </h3>
        <div id="client-emails">
            <?php
            if ($leadForm->viewPermission) :
                $nr = 0;
                foreach ($leadForm->getClientEmail() as $key => $_email) {
                    /**
                     * @var $_email ClientEmail
                     */
                    echo $this->render('_formClientEmail', [
                        'key' => $_email->isNewRecord
                            ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                            : $_email->id,
                        'form' => $formClient,
                        'email' => $_email,
                        'leadForm' => $leadForm,
                        'nr' => $nr
                    ]);
                    $nr++;
                }
                ?>
                <!-- new email fields -->
                <div id="client-new-email-block" style="display: none;">
                    <?php $newEmail = new ClientEmail(); ?>
                    <?= $this->render('_formClientEmail', [
                        'key' => '__id__',
                        'form' => $formClient,
                        'email' => $newEmail,
                        'leadForm' => $leadForm,
                        'nr' => $nr
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        if ($leadForm->mode != $leadForm::VIEW_MODE) :
            echo Html::button('<i class="fa fa-plus"></i> <span>Add email</span>', [
                'id' => 'client-new-email-button',
                'class' => 'btn btn-primary'
            ]);
            ob_start(); // output buffer the javascript to register later
            ?>
            <script>
                // add email button
                var email_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
                $('#client-new-email-button').on('click', function () {
                    email_k += 1;
                    $('#client-emails').append($('#client-new-email-block').html().replace(/__id__/g, 'new' + email_k));
                });

                // remove email button
                $(document).on('click', '.client-remove-email-button', function () {
                    $(this).closest('div').remove();
                });
            </script>
            <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean()));
        endif; ?>
    </div>

    <div class="sidebar__section">
        <h3 class="sidebar__subtitle">
            <i class="fa fa-phone"></i>
        </h3>
        <div id="client-phones">
            <?php
            if ($leadForm->viewPermission) :
                // existing emails fields
                $nr = 0;
                foreach ($leadForm->getClientPhone() as $key => $_phone) {
                    /**
                     * @var $_phone ClientPhone
                     */
                    echo $this->render('_formClientPhone', [
                        'key' => $_phone->isNewRecord
                            ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                            : $_phone->id,
                        'form' => $formClient,
                        'phone' => $_phone,
                        'leadForm' => $leadForm,
                        'nr' => $nr
                    ]);
                    $nr++;
                }
                ?>
                <!-- new phone fields -->
                <div id="client-new-phone-block" style="display: none;">
                    <?php $newPhone = new ClientPhone(); ?>
                    <?= $this->render('_formClientPhone', [
                        'key' => '__id__',
                        'form' => $formClient,
                        'phone' => $newPhone,
                        'leadForm' => $leadForm,
                        'nr' => $nr
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        if ($leadForm->mode != $leadForm::VIEW_MODE) :
            echo Html::button('<i class="fa fa-plus"></i> <span>Add phone</span>', [
                'id' => 'client-new-phone-button',
                'class' => 'btn btn-primary'
            ]);
            ob_start(); // output buffer the javascript to register later
            ?>
            <script>
                // add phone button
                var phone_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
                $('#client-new-phone-button').on('click', function () {
                    phone_k += 1;
                    $('#client-phones').append($('#client-new-phone-block').html().replace(/__id__/g, 'new' + phone_k));
                    var phoneId = '<?= strtolower($newPhone->formName()) ?>-new' + phone_k + '-phone';
                    $('#' + phoneId).intlTelInput({"nationalMode": false, "preferredCountries": ["us"]});
                });

                // remove phone button
                $(document).on('click', '.client-remove-phone-button', function () {
                    $(this).closest('div').remove();
                });
            </script>
            <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean()));
        endif; ?>
    </div>

<?php ActiveForm::end(); ?>