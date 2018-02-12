<?= "<?php\n" ?>

namespace <?= substr($generator->namespace, 0, -1) ?>;

use yii\base\Widget;

/**
 * This is just an example.
 */
class AutoloadExample extends Widget
{
    public function run()
    {
        return "Hello!";
    }
}
