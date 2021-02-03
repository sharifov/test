<?php

namespace frontend\controllers;

use common\components\schema\Types;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;


class GraphqlController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    /**
     * @return \GraphQL\Executor\ExecutionResult
     */
    public function actionIndex(): \GraphQL\Executor\ExecutionResult
    {

        $query = \Yii::$app->request->get('query', \Yii::$app->request->post('query'));
        $variables = \Yii::$app->request->get('variables', \Yii::$app->request->post('variables'));
        $operation = \Yii::$app->request->get('operation', \Yii::$app->request->post('operation', null));

        if (!empty($variables) && !is_array($variables)) {
            try {
                $variables = Json::decode($variables);
            } catch (\Throwable $e) {
                $variables = null;
            }
        }

        $schema = new Schema([
            'query' => Types::query(),
        ]);

        $result = GraphQL::executeQuery(
            $schema,
            $query,
            null,
            null,
            empty($variables) ? null : $variables,
            empty($operation) ? null : $operation
        );

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}
