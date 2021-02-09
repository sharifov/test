<?php

namespace frontend\controllers;

use common\components\schema\Types;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;

class GraphqlController extends FController
{
    public $enableCsrfValidation = false;
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
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = \Yii::$app->request->get('query', \Yii::$app->request->post('query'));
        $variables = \Yii::$app->request->get('variables', \Yii::$app->request->post('variables'));
        $operation = \Yii::$app->request->get('operation', \Yii::$app->request->post('operation', null));



        if (empty($query)) {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);
            $query = $input['query'];
            $variables = isset($input['variables']) ? $input['variables'] : [];
            $operation = isset($input['operation']) ? $input['operation'] : null;
        }

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


        return $result;
    }
}
