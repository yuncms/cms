<?php

namespace yuncms\models;

use Yii;
use yii\helpers\Json;
use yii\base\DynamicModel;
use yii\base\InvalidArgumentException;

/**
 * This is the model class for table "settings".
 *
 * @author Aris Karageorgos <aris@phe.me>
 */
class Settings extends BaseSetting
{
    /**
     * @param bool $forDropDown if false - return array or validators, true - key=>value for dropDown
     * @return array
     */
    public function getTypes($forDropDown = true)
    {
        $values = [
            'string' => ['value', 'string'],
            'integer' => ['value', 'integer'],
            'boolean' => ['value', 'boolean', 'trueValue' => "1", 'falseValue' => "0", 'strict' => true],
            'float' => ['value', 'number'],
            'email' => ['value', 'email'],
            'ip' => ['value', 'ip'],
            'url' => ['value', 'url'],
            'object' => [
                'value',
                function ($attribute) {
                    $object = null;
                    try {
                        Json::decode($this->$attribute);
                    } catch (InvalidArgumentException $e) {
                        $this->addError($attribute, Yii::t('yuncms', '"{attribute}" must be a valid JSON object', [
                            'attribute' => $attribute,
                        ]));
                    }
                }
            ],
        ];

        if (!$forDropDown) {
            return $values;
        }

        $return = [];
        foreach ($values as $key => $value) {
            $return[$key] = Yii::t('yuncms', $key);
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['section', 'key'], 'string', 'max' => 255],
            [
                ['key'],
                'unique',
                'targetAttribute' => ['section', 'key'],
                'message' =>
                    Yii::t('yuncms', '{attribute} "{value}" already exists for this section.')
            ],
            ['type', 'in', 'range' => array_keys($this->getTypes(false))],
            [['type', 'created', 'modified'], 'safe'],
            [['active'], 'boolean'],
        ];
    }

    /**
     * @param $insert
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeSave($insert)
    {
        $validators = $this->getTypes(false);
        if (!array_key_exists($this->type, $validators)) {
            $this->addError('type', Yii::t('yuncms', 'Please select correct type'));
            return false;
        }

        $model = DynamicModel::validateData([
            'value' => $this->value
        ], [
            $validators[$this->type],
        ]);

        if ($model->hasErrors()) {
            $this->addError('value', $model->getFirstError('value'));
            return false;
        }

        if ($this->hasErrors()) {
            return false;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yuncms', 'ID'),
            'type' => Yii::t('yuncms', 'Type'),
            'section' => Yii::t('yuncms', 'Section'),
            'key' => Yii::t('yuncms', 'Key'),
            'value' => Yii::t('yuncms', 'Value'),
            'active' => Yii::t('yuncms', 'Active'),
            'created' => Yii::t('yuncms', 'Created'),
            'modified' => Yii::t('yuncms', 'Modified'),
        ];
    }
}
