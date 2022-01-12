<?php

use src\model\clientChatForm\entity\ClientChatForm;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var src\model\clientChatForm\entity\ClientChatForm $model */

$this->title = $model->ccf_key;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-form-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-6">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccf_id], ['class' => 'btn btn-primary']) ?>
<?php echo Html::a(
    Html::tag('i', ' Builder', ['class' => 'fa fa-cog']),
    ['builder','id' => $model->ccf_id],
    ['class' => 'btn btn-secondary']
)?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccf_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ccf_id',
                'ccf_key',
                'ccf_name',
                'ccf_project_id:projectName',
                'ccf_enabled:booleanByLabel',
                'ccf_created_user_id:userName',
                'ccf_updated_user_id:userName',
                'ccf_created_dt:byUserDateTime',
                'ccf_updated_dt:byUserDateTime',
                [
                    'attribute' => 'ccf_dataform_json',
                    'value' => static function (ClientChatForm $model) {
                        return '<pre>' . VarDumper::dumpAsString($model->ccf_dataform_json, 10, true) . '</pre>';
                    },
                    'format' => 'raw',
                ],
            ],
        ]) ?>

    </div>

</div>
