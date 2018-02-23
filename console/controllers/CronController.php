<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\core\console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class CronController
 *
 * 0/5 * * * * /path/to/yii cron/minute >/dev/null 2>&1
 * 30 * * * * /path/to/yii cron/hourly >/dev/null 2>&1
 * 00 18 * * * /path/to/yii cron/daily >/dev/null 2>&1
 * 00 00 15 * * /path/to/yii cron/month >/dev/null 2>&1
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class CronController extends Controller
{
    /**
     * @event Event 每分钟触发事件
     */
    const EVENT_ON_MINUTE_RUN = "minute";

    /**
     * @event Event 每小时触发事件
     */
    const EVENT_ON_HOURLY_RUN = "hourly";

    /**
     * @event Event 每天触发事件
     */
    const EVENT_ON_DAILY_RUN = "daily";

    /**
     * @event Event 每月触发事件
     */
    const EVENT_ON_MONTH_RUN = "month";

    /**
     * Executes minute cron tasks.
     * @return int
     */
    public function actionMinute()
    {
        $this->stdout("Executing minute tasks:" . PHP_EOL, Console::FG_YELLOW);
        $this->trigger(self::EVENT_ON_MINUTE_RUN);
        Yii::$app->settings->set('cronLastMinuteRun', time(), 'core');
        return ExitCode::OK;
    }

    /**
     * Executes hourly cron tasks.
     * @return int
     */
    public function actionHourly()
    {
        $this->stdout("Executing hourly tasks:" . PHP_EOL, Console::FG_YELLOW);
        $this->trigger(self::EVENT_ON_HOURLY_RUN);
        Yii::$app->settings->set('cronLastHourlyRun', time(), 'core');
        return ExitCode::OK;
    }

    /**
     * Executes daily cron tasks.
     * @return int
     */
    public function actionDaily()
    {
        $this->stdout("Executing daily tasks:" . PHP_EOL, Console::FG_YELLOW);
        $this->trigger(self::EVENT_ON_DAILY_RUN);
        Yii::$app->settings->set('cronLastDailyRun', time(), 'core');
        return ExitCode::OK;
    }

    /**
     * Executes month cron tasks.
     * @return int
     */
    public function actionMonth()
    {
        $this->stdout("Executing month tasks:" . PHP_EOL, Console::FG_YELLOW);
        $this->trigger(self::EVENT_ON_MONTH_RUN);
        Yii::$app->settings->set('cronLastMonthRun', time(), 'core');
        return ExitCode::OK;
    }
}