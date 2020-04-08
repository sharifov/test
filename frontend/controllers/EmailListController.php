<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\model\emailList\entity\EmailList;
use sales\model\emailList\entity\search\EmailListSearch;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class EmailListController extends FController
{
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new EmailListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new EmailList(['el_enabled' => 1]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->el_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->el_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string|null $q
     * @param int|null $id
     * @return Response
     */
    public function actionListAjax(?string $q = null, ?int $id = null): Response
    {
        $out = ['results' => ['id' => '', 'text' => '', 'selection' => '']];

        if ($q !== null) {
            $data = EmailList::find()->select(['id' => 'el_id', 'text' => 'el_email', 'enabled' => 'el_enabled', 'title' => 'el_title'])
                ->where(['like', 'el_email', $q])
                ->orWhere(['el_id' => (int)$q])
                ->orWhere(['like', 'el_title', $q])
                ->limit(20)
                //->indexBy('id')
                ->asArray()
                ->all();

            if ($data) {
                foreach ($data as $n => $item) {
                    $text = $item['enabled'] ? $item['text'] . ' (' . $item['id'] . ')' : ''  . $item['text'] . ' (' . $item['id'] . ')' . ' <span style="color:red"><b>DISABLED</b></span>';
                    if ($item['title']) {
                        $text .= ' - ' . Html::encode($item['title']);
                    }
                    $data[$n]['text'] = $this->formatText($text, $q);
                    $data[$n]['selection'] = $item['text'];
                }
            }

            $out['results'] = $data; //array_values($data);
        } elseif ($id > 0) {
            $email = EmailList::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $email ? $email->el_email : '', 'selection' => $email ? $email->el_email : ''];
        }
        return $this->asJson($out);
    }

    private function formatText(string $str, string $term): string
    {
        return preg_replace('~' . $term . '~i', '<b style="color: #e15554"><u>$0</u></b>', $str);
    }

    protected function findModel($id): EmailList
    {
        if (($model = EmailList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
