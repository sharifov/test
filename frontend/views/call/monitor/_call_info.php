<?php

/**
 * @var Call $call
 * @var \sales\guards\call\CallDisplayGuard $callGuard
 * @var \yii\web\View $this
 */

use common\models\Call;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\widgets\DetailView;

?>

<div id="modal-call-info">
    <div class="row">
        <div class="col-md-12">
            <?= DetailView::widget([
                'model' => $call,
                'attributes' => [
                    'c_id',
                    'c_call_sid',
                    'c_parent_call_sid',
                    'c_conference_sid',

                    [
                        'attribute' => 'c_call_type_id',
                        'value' => static function (\common\models\Call $model) {
                            return $model->getCallTypeName();
                        },
                    ],
                    'c_from',
                    'c_to',
                    'c_call_status',
                    [
                        'attribute' => 'c_status_id',
                        'value' => static function (\common\models\Call $model) {
                            return $model->getStatusName();
                        },
                    ],
                    'c_forwarded_from',
                    'c_caller_name',
                    'c_call_duration',
                    [
                        'attribute' => 'c_client_id',
                        'value' => static function (Call $model) {
                            return  $model->c_client_id ?: '-';
                        },
                    ],
                    [
                        'label' => 'Department',
                        'attribute' => 'c_dep_id',
                        'value' => static function (Call $model) {
                            return $model->cDep ? $model->cDep->dep_name : '-';
                        },
                    ],
                    [
                        'label' => 'UserGroups',
                        //'attribute' => 'c_dep_id',
                        'value' => static function (Call $model) {
                            $userGroupList = [];
                            if ($model->cugUgs) {
                                foreach ($model->cugUgs as $userGroup) {
                                    $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                                }
                            }
                            return $userGroupList ? implode(' ', $userGroupList) : '-';
                        },
                        'format' => 'raw'
                    ],
                    'c_language_id',
                    'c_recording_disabled:booleanByLabel',
                ],
            ]) ?>
        </div>
        <div class="col-md-12">
            <div class="d-flex">
                <add-user :show="<?= Auth::can('call/assignUsers', ['call' => $call]) ? 'true' : 'false' ?>" :call-id="<?= $call->c_id ?>"></add-user>

                <join-user :show="<?= $callGuard->canDisplayJoinUserBtn($call, Auth::user()) ? 'true' : 'false'?>"
                           :join-listen-source="<?= Call::SOURCE_LISTEN ?>"
                           :join-coach-source="<?= Call::SOURCE_COACH ?>"
                           :join-barge-source="<?= Call::SOURCE_BARGE ?>"
                           :call-sid="'<?= $call->c_call_sid ?>'"
                ></join-user>
            </div>
        </div>
    </div>
</div>

<?php

$js = <<<JS

Vue.createApp({
    components: {
        'join-user': callJoinUserComponent,
        'add-user': callAddUserComponent
    }
}).mount('#modal-call-info');

JS;

$this->registerJs($js);



