<?php

namespace svit\tools\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SqlQueryJob extends BaseObject implements JobInterface
{
    public $sql;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     * @throws \yii\db\Exception
     */
    public function execute($queue)
    {
        \Yii::$app->db->createCommand($this->sql)->execute();
    }
}
