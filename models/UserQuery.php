<?php

namespace yuncms\models;

use yii\db\ActiveQuery;
use creocoder\taggable\TaggableQueryBehavior;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @method ActiveQuery anyTagValues($values, $attribute = null)
 * @method ActiveQuery allTagValues($values, $attribute = null)
 * @method ActiveQuery relatedByTagValues($values, $attribute = null)
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /*public function active()
    {
        return $this->andWhere(['status' => User::STATUS_PUBLISHED]);
    }*/

    /**
     * 关联tag搜索
     * @return array
     */
    public function behaviors()
    {
        return [
            TaggableQueryBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
