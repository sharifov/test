<?php

namespace sales\repositories\emailUnsubscribe;

use common\models\EmailUnsubscribe;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class EmailUnsubscribeRepository
 */
class EmailUnsubscribeRepository extends Repository
{
    /**
     * @param string $email
     * @param int $projectId
     * @return EmailUnsubscribe
     */
    public function find(string $email, int $projectId): EmailUnsubscribe
    {
        if ($source = EmailUnsubscribe::findOne(['eu_email' => $email, 'eu_project_id' => $projectId])) {
            return $source;
        }
        throw new NotFoundException('EmailUnsubscribe is not found');
    }

    /**
     * @param EmailUnsubscribe $model
     * @param bool $validate
     * @return EmailUnsubscribe
     */
    public function save(EmailUnsubscribe $model, bool $validate = false): EmailUnsubscribe
    {
        if (!$model->save($validate)) {
            throw new \RuntimeException('Saving error');
        }
        return $model;
    }

    /**
     * @param EmailUnsubscribe $model
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(EmailUnsubscribe $model): void
    {
        if (!$model->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }

}