<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\jobs;

/**
 * 下载任务
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class DownloadJob extends BaseJob
{
    /**
     * @var string 下载Url
     */
    public $url;

    /**
     * @var string 存储路径
     */
    public $file;

    /**
     * @param \yii\queue\Queue $queue
     */
    public function execute($queue)
    {
        file_put_contents($this->file, file_get_contents($this->url));
    }
}