<?php

use yii\db\Migration;

/**
 * Class m181101_135942_add_columns_tbl_lead_flow
 */
class m181101_135942_add_columns_tbl_lead_flow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_flow}}', 'lf_from_status_id', $this->integer());
        $this->addColumn('{{%lead_flow}}', 'lf_end_dt', $this->dateTime());
        $this->addColumn('{{%lead_flow}}', 'lf_time_duration', $this->integer());
        $this->addColumn('{{%lead_flow}}', 'lf_description', $this->string(250));

        $this->createIndex('ind-lead_flow_status', '{{%lead_flow}}', ['status']);
        $this->createIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}', ['status', 'lf_from_status_id']);


        /*$q = new \yii\db\Query();
        $logPrev = $q->select(['status'])->from('lead_flow AS lf')->where('lead_id = lf.lead_id AND id < lf.id')->orderBy(['id' => SORT_DESC]);
        $logNext = $q->select(['created'])->from('lead_flow AS lf')->where('lead_id = lf.lead_id AND id > lf.id')->orderBy(['id' => SORT_ASC]);

        $logs = \common\models\LeadFlow::find()->select($logPrev)->all();



        if($logs) {
            foreach ($logs as $log) {

                $logPrev = \common\models\LeadFlow::find()->select(['status'])->where(['lead_id' => $log->lead_id])->andWhere(['<', 'id', $log->id])->orderBy(['id' => SORT_DESC])->one();
                $logNext = \common\models\LeadFlow::find()->select(['created'])->where(['lead_id' => $log->lead_id])->andWhere(['>', 'id', $log->id])->orderBy(['id' => SORT_ASC])->one();






                $from_status_id = null;
                $lf_end_dt = null;

                if($logPrev) {
                    $from_status_id = $logPrev->status;
                    //$lf_end_dt = $logPrev->created;
                }

                if($logNext) {
                    $lf_end_dt = $logNext->created;
                }

                $log->lf_from_status_id = $from_status_id;
                $log->lf_end_dt = $lf_end_dt;
                $log->lf_time_duration = $lf_end_dt ? (int) (strtotime($log->lf_end_dt) - strtotime($log->created)) : null;
                if($log->save()) {
                    echo ' '.$log->id.' - Status: '.$from_status_id.', end DT: '. $lf_end_dt . ', Duration: '. $log->lf_time_duration."\r\n";
                }

            }
        }*/

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('ind-lead_flow_status', '{{%lead_flow}}');
        $this->dropIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}');

        $this->dropColumn('{{%lead_flow}}', 'lf_from_status_id');
        $this->dropColumn('{{%lead_flow}}', 'lf_end_dt');
        $this->dropColumn('{{%lead_flow}}', 'lf_time_duration');
        $this->dropColumn('{{%lead_flow}}', 'lf_description');
    }

}
