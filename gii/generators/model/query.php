<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /*public function active()
    {
        return $this->andWhere(['status' => <?= $modelFullClassName ?>::STATUS_PUBLISHED]);
    }*/

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
<?php if(isset($labels['created_at'])): ?>

    /**
     * 热门模型
     * @param string $reference 计算字段
     * @param float $pull 热度衰减下拉指数默认是1.8
     * @return mixed
     */
    public function hottest($reference = 'views', $pull = 1.8)
    {
        return $this->orderBy(['(' . $reference . ' / pow((((UNIX_TIMESTAMP(NOW()) - created_at) / 3600) + 2),' . $pull . ') )' => SORT_DESC]);
    }

    /**
     * 查询今日新增
     * @return $this
     */
    public function dayCreate()
    {
        return $this->andWhere('date(created_at)=date(NOW())');
    }

    /**
     * 查询本周新增
     * @return $this
     */
    public function weekCreate()
    {
        return $this->andWhere('month(FROM_UNIXTIME(created_at)) = month(curdate()) AND week(FROM_UNIXTIME(created_at)) = week(curdate())');
    }

    /**
     * 查询本月新增
     * @return $this
     */
    public function monthCreate()
    {
        return $this->andWhere('month(FROM_UNIXTIME(created_at)) = month(curdate()) AND year(FROM_UNIXTIME(created_at)) = year(curdate())');
    }

    /**
     * 查询本年新增
     * @return $this
     */
    public function yearCreate()
    {
        return $this->andWhere('year(FROM_UNIXTIME(created_at)) = year(curdate())');
    }

    /**
     * 查询本季度新增
     * @return $this
     */
    public function quarterCreate()
    {
        return $this->andWhere('quarter(FROM_UNIXTIME(created_at)) = quarter(curdate())');
    }
<?php endif; ?>
}
