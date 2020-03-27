<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * Class DepartmentEmailProjectQuery
 */
class DepartmentEmailProjectQuery extends ActiveQuery
{
//    public function byEmail(string $email): self
//    {
//        return $this->where(['dep_email' => $email, 'dep_enable' => true])->limit(1);
//    }

    /**
     * @param string|array $email
     * @param bool $onyEnabled
     * @param bool $eagerLoading
     * @return $this
     */
    public function byEmail($email, bool $eagerLoading = true, bool $onyEnabled = false): self
    {
        if ($onyEnabled) {
            $this->innerJoinWith(['emailList' => static function(\sales\model\emailList\entity\Scopes $query) use ($email) {
                $query
                    ->andOnCondition(['el_enabled' => true])
                    ->andOnCondition(['el_email' => $email]);
            }], $eagerLoading);
        } else {
            $this->innerJoinWith(['emailList' => static function(\sales\model\emailList\entity\Scopes $query) use ($email) {
                $query->andOnCondition(['el_email' => $email]);
            }], $eagerLoading);
        }
        return $this->orderBy(['dep_id' => SORT_DESC]);
    }

    public function withEmailList(bool $onyEnabled = false): self
    {
        if ($onyEnabled) {
            return $this->with(['emailList' => static function(\sales\model\emailList\entity\Scopes $query) {
                $query->enabled();
            }]);
        }
        return $this->with(['emailList']);
    }
}
