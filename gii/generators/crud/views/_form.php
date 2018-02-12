<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>
use yii\helpers\Html;
use xutl\inspinia\ActiveForm;

/* @var \yii\web\View $this */
/* @var <?= ltrim($generator->modelClass, '\\') ?> $model */
/* @var ActiveForm $form */
?>
<?= "<?php " ?>$form = ActiveForm::begin(['layout'=>'horizontal', 'enableAjaxValidation' => true, 'enableClientValidation' => false,]); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
echo "    <?= " . $generator->generateActiveField($attribute) . " ?>";
echo "    <div class=\"hr-line-dashed\"></div>\n\n";
    }
} ?>

<div class="form-group">
    <div class="col-sm-4 col-sm-offset-2">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create');?> : <?= $generator->generateString('Update');?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
</div>

<?= "<?php " ?>ActiveForm::end(); ?>

