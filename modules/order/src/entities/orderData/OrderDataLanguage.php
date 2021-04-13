<?php

namespace modules\order\src\entities\orderData;

use common\models\Language;
use sales\model\project\entity\projectLocale\ProjectLocale;
use yii\helpers\VarDumper;
use yii\validators\StringValidator;

/**
 * Class OrderDataLanguage
 *
 * @property string|null $languageId
 */
class OrderDataLanguage
{
    private ?string $languageId;

    private function __construct(?string $languageId)
    {
        $this->languageId = $languageId;
    }

    public function getValue(): ?string
    {
        return $this->languageId;
    }

    public static function create(int $orderId, $languageId, int $projectId, string $action): self
    {
        if (!$languageId) {
            \Yii::warning([
                'message' => 'Language Id error',
                'error' => 'Is empty',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataLanguage');
            return self::default($projectId);
        }

        $stringValidator = new StringValidator(['max' => 5, 'min' => 1]);
        if (!$stringValidator->validate($languageId, $error)) {
            \Yii::warning([
                'message' => 'Language Id error',
                'languageId' => VarDumper::dumpAsString($languageId),
                'error' => $error,
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataLanguage');
            return self::default($projectId);
        }

        $language = Language::find()->andWhere(['language_id' => $languageId])->exists();
        if (!$language) {
            \Yii::warning([
                'message' => 'Language Id error',
                'languageId' => VarDumper::dumpAsString($languageId),
                'error' => 'Language Id is not found',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataLanguage');
            return self::default($projectId);
        }

        return new self($languageId);
    }

    public static function default(int $projectId): self
    {
        $languageId = null;
        $projectLocale = ProjectLocale::find()
            ->select('pl_language_id')
            ->enabled()
            ->default()
            ->byProject($projectId)
            ->asArray()
            ->one();
        if ($projectLocale && $projectLocale['pl_language_id']) {
            $languageId = $projectLocale['pl_language_id'];
        }
        return new self($languageId);
    }
}
