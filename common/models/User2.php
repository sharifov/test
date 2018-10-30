<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class User2 extends \webvimark\modules\UserManagement\models\User
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employees}}';
    }


}
