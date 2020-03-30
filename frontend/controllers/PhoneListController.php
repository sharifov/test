<?php

namespace frontend\controllers;

use sales\auth\Auth;
use Yii;
use sales\model\phoneList\entity\PhoneList;
use sales\model\phoneList\entity\search\PhoneListSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class PhoneListController extends FController
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
        $searchModel = new PhoneListSearch();
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
        $model = new PhoneList(['pl_enabled' => 1]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pl_id]);
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
            return $this->redirect(['view', 'id' => $model->pl_id]);
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
            $query = PhoneList::find()->select(['id' => 'pl_id', 'text' => 'pl_phone_number', 'title' => 'pl_title']);

            //if (is_numeric($q)) {}
            $query->where(['like', 'pl_phone_number', $q])
                ->orWhere(['pl_id' => (int)$q]);
            $query->orWhere(['like', 'pl_title', $q]);

            $query->limit(20)
                //->indexBy('id')
                ->asArray();
            $data = $query->all();

            if ($data) {
                foreach ($data as $n => $item) {
                    $text = $item['text'] . ' (' . $item['id'] . ')';
                    if ($item['title']) {
                        $text .= ' - ' . Html::encode($item['title']);
                    }
                    $data[$n]['text'] = $this->formatText($text, $q);
                    $data[$n]['selection'] = $item['text'];
                }
            }

            $out['results'] = $data; //array_values($data);
        } elseif ($id > 0) {
            $phone = PhoneList::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $phone ? $phone->pl_phone_number : '', 'selection' => $phone ? $phone->pl_phone_number : ''];
        }
        return $this->asJson($out);
    }

    private function formatText(string $str, string $term): string
    {
        $term = str_replace('+', '\+', $term);
        return preg_replace('~' . $term . '~i', '<b style="color: #e15554"><u>$0</u></b>', $str);
    }

    protected function findModel($id): PhoneList
    {
        if (($model = PhoneList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
