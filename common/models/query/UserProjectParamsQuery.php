<?php

namespace common\models\query;

use common\models\UserProjectParams;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[UserProjectParams]].
 *
 * @see UserProjectParams
 */
class UserProjectParamsQuery extends ActiveQuery
{
    public function byPhone(string $phone): self
    {
        return $this->where(['upp_tw_phone_number' => $phone])->orderBy(['upp_created_dt' => SORT_DESC]);
    }

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
        return $this->orderBy(['upp_created_dt' => SORT_DESC]);
    }

//    public function byEmail(string $email): self
//    {
//        return $this->where(['upp_email' => $email])->orderBy(['upp_created_dt' => SORT_DESC]);
//    }

    public function withPhoneList(bool $onyEnabled = false): self
    {
        if ($onyEnabled) {
            return $this->with(['phoneList' => static function(\sales\model\phoneList\entity\Scopes $query) {
                $query->enabled();
            }]);
        }
        return $this->with(['phoneList']);
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
