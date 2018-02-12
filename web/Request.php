<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\web;

use yuncms\base\RequestTrait;

/**
 * Class Request
 * @package yuncms\web
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class Request extends \yii\web\Request
{
    use RequestTrait;

    public $ipHeaders = [
        'Client-IP',
        'X-Forwarded-For',
        'X-Forwarded',
        'X-Cluster-Client-IP',
        'X-REAL-IP',
        'Forwarded-For',
        'Forwarded',
        'RemoteIp'
    ];

    public $secureProtocolHeaders = [
        'X-Forwarded-Proto' => ['https'],
        'Front-End-Https' => ['on'],
        'X-CLIENT-SCHEME' => ['https'],
        'X-Client-Proto' => ['https'],
    ];

}