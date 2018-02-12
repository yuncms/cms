<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use <?= $generator->baseClass?>;
use yii\db\Connection;
<?php if(isset($labels['slug']) || isset($labels['uuid'])): ?>
use yii\helpers\Inflector;
<?php endif; ?>
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use yii\caching\ChainedDependency;
<?php if(isset($labels['created_at'])): ?>
use yii\behaviors\TimestampBehavior;
<?php endif; ?>
<?php if(isset($labels['user_id'])): ?>
use yii\behaviors\BlameableBehavior;
<?php endif; ?>
<?php if(isset($labels['slug']) || isset($labels['uuid'])): ?>
use yii\behaviors\AttributeBehavior;
<?php endif; ?>
use yuncms\core\helpers\DateHelper;
use yuncms\core\ScanInterface;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 *
 * @property-read boolean $isAuthor 是否是作者
<?php if(isset($labels['status'])): ?>
 * @property-read boolean $isDraft 是否草稿
 * @property-read boolean $isPublished 是否发布
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') ?><?php if(isset($labels['status'])): ?> implements ScanInterface<?php endif; ?>
{

    //场景定义
    const SCENARIO_CREATE = 'create';//创建
    const SCENARIO_UPDATE = 'update';//更新
<?php if(isset($labels['status'])): ?>
    //状态定义
    const STATUS_DRAFT = 0b0;//草稿
    const STATUS_REVIEW = 0b1;//待审核
    const STATUS_REJECTED = 0b10;//拒绝
    const STATUS_PUBLISHED = 0b11;//发布

    //事件定义
    const BEFORE_PUBLISHED = 'beforePublished';
    const AFTER_PUBLISHED = 'afterPublished';
    const BEFORE_REJECTED = 'beforeRejected';
    const AFTER_REJECTED = 'afterRejected';
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }

    /**
     * 定义行为
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
<?php if (isset($labels['created_at']) && isset($labels['updated_at'])): ?>
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::className()
        ];
<?php elseif(isset($labels['created_at'])): ?>
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
            ],
        ];
<?php endif; ?>
<?php if(isset($labels['user_id'])): ?>
        $behaviors['user'] = [
            'class' => BlameableBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['user_id']
            ],
        ];
<?php endif; ?>
<?php if(isset($labels['slug'])): ?>
        $behaviors['slug'] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['slug']
            ],
            'value' => function ($event) {
                return Inflector::slug($event->sender->title);
            }
        ];
<?php endif; ?>
<?php if(isset($labels['uuid'])): ?>
        $behaviors['uuid'] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_AFTER_INSERT => ['uuid']
            ],
            'value' => function ($event) {
                return $event->sender->generateSlug();
            }
        ];
<?php endif; ?>
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            static::SCENARIO_CREATE => [],
            static::SCENARIO_UPDATE => [],
        ]);
    }

<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>    <?php if(isset($labels['status'])): ?>// status rule
            ['status', 'default', 'value' => self::STATUS_REVIEW],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_REVIEW, self::STATUS_REJECTED, self::STATUS_PUBLISHED]],<?php endif; ?>
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }

    /**
     * 是否是作者
     * @return bool
     */
    public function getIsAuthor()
    {
        return $this->user_id == Yii::$app->user->id;
    }
<?php if(isset($labels['status'])): ?>
    /**
     * 是否草稿状态
     * @return bool
     */
    public function isDraft()
    {
        return $this->status == static::STATUS_DRAFT;
    }

    /**
     * 是否发布状态
     * @return bool
     */
    public function isPublished()
    {
        return $this->status == static::STATUS_PUBLISHED;
    }

    /**
     * 机器审核
     * @param int $id model id
     * @param string $suggestion the ID to be looked for
     * @return void
     */
    public static function review($id, $suggestion)
    {
        if (($model = static::findOne($id)) != null) {
            if ($suggestion == 'pass') {
                $model->setPublished();
            } elseif ($suggestion == 'block') {
                $model->setRejected('');
            } elseif ($suggestion == 'review') { //人工审核，不做处理
                return;
            }
        }
    }

    /**
     * 获取待审
     * @param int $id
     * @return string 待审核的内容字符串
     */
    public static function findReview($id)
    {
        if (($model = static::findOne($id)) != null) {
            return $model->content;
        }
        return null;
    }

    /**
     * 审核通过
     * @return int
     */
    public function setPublished()
    {
        $this->trigger(self::BEFORE_PUBLISHED);
        $rows = $this->updateAttributes(['status' => static::STATUS_PUBLISHED, 'published_at' => time()]);
        $this->trigger(self::AFTER_PUBLISHED);
        return $rows;
    }

    /**
     * 拒绝通过
     * @param string $failedReason 拒绝原因
     * @return int
     */
    public function setRejected($failedReason)
    {
        $this->trigger(self::BEFORE_REJECTED);
        $rows = $this->updateAttributes(['status' => static::STATUS_REJECTED, 'failed_reason' => $failedReason]);
        $this->trigger(self::AFTER_REJECTED);
        return $rows;
    }

    /**
     * 获取状态列表
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_DRAFT => Yii::t('<?=$generator->messageCategory?>', 'Draft'),
            self::STATUS_REVIEW => Yii::t('<?=$generator->messageCategory?>', 'Review'),
            self::STATUS_REJECTED => Yii::t('<?=$generator->messageCategory?>', 'Rejected'),
            self::STATUS_PUBLISHED => Yii::t('<?=$generator->messageCategory?>', 'Published'),
        ];
    }
<?php endif; ?>
//    public function afterFind()
//    {
//        parent::afterFind();
//        // ...custom code here...
//    }

    /**
     * @inheritdoc
     */
//    public function beforeSave($insert)
//    {
//        if (!parent::beforeSave($insert)) {
//            return false;
//        }
//
//        // ...custom code here...
//        return true;
//    }

    /**
     * @inheritdoc
     */
//    public function afterSave($insert, $changedAttributes)
//    {
//        parent::afterSave($insert, $changedAttributes);
//        Yii::$app->queue->push(new ScanTextJob([
//            'modelId' => $this->getPrimaryKey(),
//            'modelClass' => get_class($this),
//            'scenario' => $this->isNewRecord ? 'new' : 'edit',
//            'category'=>'',
//        ]));
//        // ...custom code here...
//    }

    /**
     * @inheritdoc
     */
//    public function beforeDelete()
//    {
//        if (!parent::beforeDelete()) {
//            return false;
//        }
//        // ...custom code here...
//        return true;
//    }

    /**
     * @inheritdoc
     */
//    public function afterDelete()
//    {
//        parent::afterDelete();
//
//        // ...custom code here...
//    }

    /**
     * 生成一个独一无二的标识
     */
    protected function generateSlug()
    {
        $result = sprintf("%u", crc32($this->id));
        $slug = '';
        while ($result > 0) {
            $s = $result % 62;
            if ($s > 35) {
                $s = chr($s + 61);
            } elseif ($s > 9 && $s <= 35) {
                $s = chr($s + 55);
            }
            $slug .= $s;
            $result = floor($result / 62);
        }
        //return date('YmdHis') . $slug;
        return $slug;
    }

    /**
     * 获取模型总数
     * @param null|int $duration 缓存时间
     * @return int get the model rows
     */
    public static function getTotal($duration = null)
    {
        $total = static::getDb()->cache(function (Connection $db) {
            return static::find()->count();
        }, $duration, new ChainedDependency([
            'dependencies' => new DbDependency(['db' => self::getDb(), 'sql' => 'SELECT MAX(id) FROM ' . self::tableName()])
        ]));
        return $total;
    }
<?php if(isset($labels['created_at'])): ?>

    /**
     * 获取模型今日新增总数
     * @param null|int $duration 缓存时间
     * @return int
     */
    public static function getTodayTotal($duration = null)
    {
        $total = static::getDb()->cache(function (Connection $db) {
            return static::find()->where(['between', 'created_at', DateHelper::todayFirstSecond(), DateHelper::todayLastSecond()])->count();
        }, $duration, new ChainedDependency([
            'dependencies' => new DbDependency(['db' => self::getDb(), 'sql' => 'SELECT MAX(created_at) FROM ' . self::tableName()])
        ]));
        return $total;
    }
<?php endif; ?>
<?php endif; ?>
}
