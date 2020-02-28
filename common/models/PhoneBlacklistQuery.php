<?php

namespace common\models;

use Yii;

/**
 * Class PhoneBlacklistQuery
 */
class PhoneBlacklistQuery extends \yii\db\ActiveQuery
{
    /**
     * @return PhoneBlacklistQuery
     */
    public function active(): PhoneBlacklistQuery
    {
        return $this->andWhere(['pbl_enabled' => true]);
    }

    /**
     * @return PhoneBlacklistQuery
     */
    public function activeByExpired(): PhoneBlacklistQuery
    {
        return $this->andWhere([
            'OR',
            ['IS', 'pbl_expiration_date', NULL],
            ['>=', 'pbl_expiration_date', date('Y-m-d')]
        ]);
    }

    /**
     * @param string $phone
     * @return bool
     * @throws \yii\db\Exception
     */
    public function isExists(string $phone): bool
    {
        $query = $this->select(['pbl_phone'])
            ->where(['pbl_phone' => $phone]);

        if ($patternsPhone = self::getRegexPatternsPhone()) {
            $strPatternsPhone = $this->preparePatternsPhone($patternsPhone);
            $query->orWhere($this->checkByRegexp($phone, $strPatternsPhone));
        }

        $query->active()->activeByExpired();

        return $query->exists();
    }

    /**
     * @param string $phone
     * @param string $pattern
     * @return false|string|null
     * @throws \yii\db\Exception
     */
    private function checkByRegexp(string $phone, string $pattern)
    {
        $sql = "SELECT '" . $phone . "' REGEXP '" . $pattern . "'";
        return Yii::$app->db->createCommand($sql)->queryScalar();
    }

    /**
     * @param array $patternsPhone
     * @return string
     */
    private function preparePatternsPhone(array $patternsPhone): string
    {
        $result = implode('|', $patternsPhone);
        return str_replace('+', '\\\+', $result);
    }

    /**
     * @return array
     */
    private static function getRegexPatternsPhone(): array
    {
        return PhoneBlacklist::find()->select(['pbl_phone'])
            ->where(['REGEXP', 'pbl_phone', '\\*|\\.'])
            ->active()
            ->activeByExpired()
            ->column();
    }
}
