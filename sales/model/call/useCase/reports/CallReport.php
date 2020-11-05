<?php

namespace sales\model\call\useCase\reports;

use common\models\Call;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\FileHelper;

class CallReport
{
    private const PHONES = [
        '+18559404266',
        '+18559404246',
        '+18559404224',
        '+18559404288',
    ];

    public function generate(): void
    {
        $params = \Yii::$app->params['price_line_ftp_credential'];
        $newFileName = 'Test1_Call_Report_' . date('Y-m-d') . '.csv';

        $result = $this->getResult(self::PHONES);
        $file = $this->writeTmpFile($result);
        $this->send($file, $newFileName, $params);
//        FileHelper::unlink($file);
    }

    private function send(string $file, $newFileName, $params)
    {
//        $this->withSFTP($file, $newFileName, $params);
//        $this->withCurl($file, $newFileName, $params);
    }

    private function withSFTP($file, $newFileName, $params)
    {
        try {
            $sftp = new SFTPConnection($params['url'], $params['port']);
            $sftp->login($params['user'], $params['pass']);
            $sftp->uploadFile($file, $params['path'] . '/' . $newFileName);
        } catch (\Throwable $e) {
            \Yii::error($e);
        }
    }

    private function withCurl($file, $newFileName, $params)
    {
        $url = $params['protocol'] . '://' . $params['url'] . ':' . $params['port'] . '/' . $params['path'] . '/' . $newFileName;

        $ch = curl_init($url);

        $fh = fopen($file, 'r');

        if ($fh) {
            curl_setopt($ch, CURLOPT_USERPWD, $params['user'] . ':' . $params['pass']);
            curl_setopt($ch, CURLOPT_UPLOAD, true);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
            curl_setopt($ch, CURLOPT_INFILE, $fh);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response) {
                return true;
            }
            return false;
        }
    }

    private function writeTmpFile($data): string
    {
        $file = \Yii::getAlias('@runtime/call_report_tmp.csv');

        $fp = fopen($file, 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        return $file;
    }

    private function getResult(array $phones): array
    {
        $query = (new Query())
            ->select([
                '`Time Stamp (UTC)`' => 'c_created_dt',
                '`Call ID`' => 'c_id',
                '`Call Length`',
                '`Phone number`' => 'c_to',
            ])
            ->from(Call::tableName())
            ->leftJoin(
                [
                    'ss' => (new Query())
                        ->select([
                            'c_parent_id',
                            '`Call Length`' => 'sum(c_recording_duration)'
                        ])
                        ->from(Call::tableName())
                        ->groupBy('c_parent_id')
                ],
                Call::tableName() . '.c_id = ss.c_parent_id'
            )
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(['c_to' => $phones])
            ->andWhere(['>=', 'c_created_dt', new Expression('date(now()) - interval 1 day')])
            ->andWhere(['<', 'c_created_dt', new Expression('date(now())')])
            ->orderBy(['`Time Stamp (UTC)`' => SORT_ASC]);
        return $query->all();
    }
}
