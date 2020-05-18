<?php

use common\models\Department;
use common\models\Employee;
use common\models\UserDepartment;
use yii\db\Migration;

/**
 * Class m190821_081604_update_user_departments
 */
class m190821_081604_update_user_departments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $department = Department::DEPARTMENT_SALES;
        $count = 0;
        $errCount = 0;
        foreach (Employee::find()->all() as $user) {
            if (!UserDepartment::find()->andWhere(['ud_user_id' => $user->id, 'ud_dep_id' => $department])->limit(1)->one()) {
                $ud = new UserDepartment(['ud_user_id' => $user->id, 'ud_dep_id' => $department]);
                if ($ud->save()) {
                    $count++;
                } else {
                    $errCount++;
                }
            }
        }
        echo PHP_EOL;
        echo 'Count updated users: ' . $count;
        echo PHP_EOL;
        if ($errCount) {
            echo 'Count error updated users: ' . $errCount;
        }
        echo PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $department = Department::DEPARTMENT_SALES;
        $count = UserDepartment::deleteAll(['ud_dep_id' => $department]);
        echo PHP_EOL;
        echo 'Count updated users: ' . $count;
        echo PHP_EOL;
    }

}
