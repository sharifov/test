<?php

namespace sales\access;

use common\models\Client;
use common\models\Employee;
use common\models\UserContactList;

/**
 * Class ContactUpdateAccess
 */
class ContactUpdateAccess
{
    /**
     * @param Client $client
     * @param Employee $user
     * @return bool
     */
    public function isUserCanUpdateContact(Client $client, Employee $user): bool
	{
		return (
			$user->isAdmin() ||
			$user->isSuperAdmin() ||
			$this->isContactPublicOwner($client->id, $user->id)
		);
	}

	private function isContactPublicOwner(int $clientId, int $userId): bool
    {
        return Client::find()
            ->innerJoin(UserContactList::tableName() . ' AS user_contact_list',
                'user_contact_list.ucl_client_id = ' . Client::tableName() . '.id')
            ->where(['ucl_user_id' => $userId])
            ->andWhere(['ucl_client_id' => $clientId])
            ->andWhere(['is_public' => false])
            ->exists();
    }
}
