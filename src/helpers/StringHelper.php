<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\helpers;

/**
 * Class StringHelper
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * 提取两个字符串之间的值，不包括分隔符
     *
     * @param string $string 待提取的只付出
     * @param string $start 开始字符串
     * @param string|null $end 结束字符串，省略将返回所有的。
     * @return bool string substring between $start and $end or false if either string is not found
     */
    public static function betweenStr($string, $start, $end = null)
    {
        if (($start_pos = strpos($string, $start)) !== false) {
            if ($end) {
                if (($end_pos = strpos($string, $end, $start_pos + strlen($start))) !== false) {
                    return substr($string, $start_pos + strlen($start), $end_pos - ($start_pos + strlen($start)));
                }
            } else {
                return substr($string, $start_pos);
            }
        }
        return false;
    }

    /**
     * Generates a valid v4 UUID string. See [http://stackoverflow.com/a/2040279/684]
     *
     * @return string The UUID.
     * @throws \Exception
     */
    public static function UUID(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            random_int(0, 0xffff), random_int(0, 0xffff),

            // 16 bits for "time_mid"
            random_int(0, 0xffff),

            // 16 bits for "time_hi_and_version", four most significant bits holds version number 4
            random_int(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", two most significant bits holds zero and
            // one for variant DCE1.1
            random_int(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
    }

    /**
     * Returns is the given string matches a v4 UUID pattern.
     *
     * @param string $uuid The string to check.
     *
     * @return bool Whether the string matches a v4 UUID pattern.
     */
    public static function isUUID(string $uuid): bool
    {
        return !empty($uuid) && preg_match('/[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}/ui', $uuid);
    }
}