<?php

namespace yuncms\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;
use yuncms\helpers\PasswordHelper;
use yuncms\oauth2\OAuth2IdentityInterface;
use yuncms\tag\models\Tag;
use yuncms\user\frontend\assets\UserAsset;
use yuncms\user\UserTrait;

/**
 * This is the model class for table "{{%user}}".
 *
 * Magic methods:
 * @method ActiveRecord getTagValues($asArray = null)
 * @method ActiveRecord setTagValues($values)
 * @method ActiveRecord addTagValues($values)
 * @method ActiveRecord removeTagValues($values)
 * @method ActiveRecord removeAllTagValues()
 * @method ActiveRecord hasTagValues($values)
 *
 * Database fields:
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $mobile
 * @property string $nickname
 * @property string $auth_key
 * @property string $password_hash
 * @property string $access_token
 * @property integer $avatar
 * @property string $unconfirmed_email
 * @property string $unconfirmed_mobile
 * @property string $registration_ip
 * @property integer $flags
 * @property integer $email_confirmed_at
 * @property integer $mobile_confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property-read boolean $isBlocked 账户是否锁定
 * @property-read bool $isMobileConfirmed 是否已经手机激活
 * @property-read bool $isEmailConfirmed 是否已经邮箱激活
 * @property-read bool $isAvatar 是否有头像
 *
 * Defined relations:
 * @property UserExtra $extra
 * @property UserLoginHistory[] $userLoginHistories
 * @property UserProfile $profile
 * @property UserSocialAccount[] $socialAccounts
 * @property Tag[] $tags
 * @property UserToken[] $userTokens
 *
 */
class User extends ActiveRecord implements IdentityInterface, OAuth2IdentityInterface, RateLimitInterface
{
    use UserTrait;

    //事件定义
    const BEFORE_CREATE = 'beforeCreate';
    const AFTER_CREATE = 'afterCreate';
    const BEFORE_REGISTER = 'beforeRegister';
    const AFTER_REGISTER = 'afterRegister';

    //场景定义
    const SCENARIO_CREATE = 'create';//后台或控制台创建用户
    const SCENARIO_UPDATE = 'update';//后台或控制台修改用户
    const SCENARIO_REGISTER = 'basic_create';//基本邮箱注册
    const SCENARIO_EMAIL_REGISTER = 'email_create';//邮箱注册
    const SCENARIO_MOBILE_REGISTER = 'mobile_create';//手机号注册
    const SCENARIO_SETTINGS = 'settings';//更新
    const SCENARIO_CONNECT = 'connect';//账户链接或自动注册新用户
    const SCENARIO_PASSWORD = 'password';

    // following constants are used on secured email changing process
    const OLD_EMAIL_CONFIRMED = 0b1;
    const NEW_EMAIL_CONFIRMED = 0b10;

    //头像
    const AVATAR_BIG = 'big';
    const AVATAR_MIDDLE = 'middle';
    const AVATAR_SMALL = 'small';

    /**
     * @var string Plain password. Used for model validation.
     */
    public $password;

    /**
     * @var UserProfile|null
     */
    private $_profile;

    /** @var  UserExtra|null */
    private $_extra;

    /**
     * @var string Default username regexp
     */
    public static $usernameRegexp = '/^[-a-zA-Z0-9_]+$/u';

    /**
     * @var string Default nickname regexp
     */
    public static $nicknameRegexp = '/^[-a-zA-Z0-9_\x{4e00}-\x{9fa5}\.@]+$/u';

    /**
     * @var string Default mobile regexp
     */
    public static $mobileRegexp = '/^1[34578]{1}[\d]{9}$|^166[\d]{8}$|^19[89]{1}[\d]{8}$/';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * 定义行为
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior'
            ],
            'taggable' => [
                'class' => 'creocoder\taggable\TaggableBehavior',
                'tagValuesAsArray' => true,
                'tagRelation' => 'tags',
                'tagValueAttribute' => 'id',
                'tagFrequencyAttribute' => 'frequency',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            static::SCENARIO_CREATE => ['nickname', 'email', 'password'],
            static::SCENARIO_UPDATE => ['nickname', 'email', 'password'],
            static::SCENARIO_REGISTER => ['nickname', 'password'],
            static::SCENARIO_EMAIL_REGISTER => ['nickname', 'email', 'password'],
            static::SCENARIO_MOBILE_REGISTER => ['mobile', 'password'],
            static::SCENARIO_SETTINGS => ['username', 'email', 'password'],
            static::SCENARIO_CONNECT => ['nickname', 'email', 'password'],//链接账户密码可以为空邮箱可以为空
            static::SCENARIO_PASSWORD => ['password'],//只修改密码
        ]);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username rules
            'usernameMatch' => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 50],
            'usernameUnique' => ['username', 'unique', 'message' => Yun::t('user', 'This username has already been taken')],
            'usernameTrim' => ['username', 'trim'],

            // nickname rules
            'nicknameRequired' => ['nickname', 'required', 'on' => [self::SCENARIO_EMAIL_REGISTER, self::SCENARIO_CONNECT]],
            'nicknameMatch' => ['nickname', 'match', 'pattern' => static::$nicknameRegexp],
            'nicknameLength' => ['nickname', 'string', 'min' => 3, 'max' => 255],
            'nicknameUnique' => ['nickname', 'unique', 'message' => Yun::t('user', 'This nickname has already been taken')],
            'nicknameTrim' => ['nickname', 'trim'],

            // email rules
            'emailRequired' => ['email', 'required', 'on' => [self::SCENARIO_EMAIL_REGISTER]],
            'emailPattern' => ['email', 'email', 'checkDNS' => true],
            'emailLength' => ['email', 'string', 'max' => 255],
            'emailUnique' => ['email', 'unique', 'message' => Yun::t('user', 'This email address has already been taken')],
            'emailTrim' => ['email', 'trim'],
            'emailDefault' => ['email', 'default', 'value' => null],

            //mobile rules
            'mobileRequired' => ['mobile', 'required', 'on' => [self::SCENARIO_MOBILE_REGISTER]],
            'mobilePattern' => ['mobile', 'match', 'pattern' => static::$mobileRegexp],
            'mobileLength' => ['mobile', 'string', 'max' => 11],
            'mobileUnique' => ['mobile', 'unique', 'message' => Yun::t('user', 'This phone has already been taken')],
            'mobileDefault' => ['mobile', 'default', 'value' => null],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => [self::SCENARIO_EMAIL_REGISTER, self::SCENARIO_MOBILE_REGISTER]],
            'passwordLength' => ['password', 'string', 'min' => 6],

            // tags rules
            'tags' => ['tagValues', 'safe'],


            [['flags', 'email_confirmed_at', 'mobile_confirmed_at', 'blocked_at'], 'integer'],
            [['registration_ip'], 'string', 'max' => 255],
            [['mobile', 'unconfirmed_mobile'], 'string', 'max' => 11],
            [['access_token'], 'string', 'max' => 100],
            [['unconfirmed_email'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yun::t('user', 'ID'),
            'username' => Yun::t('user', 'Username'),
            'email' => Yun::t('user', 'Email'),
            'mobile' => Yun::t('user', 'Mobile'),
            'nickname' => Yun::t('user', 'Nickname'),
            'auth_key' => Yun::t('user', 'Auth Key'),
            'password_hash' => Yun::t('user', 'Password Hash'),
            'access_token' => Yun::t('user', 'Access Token'),
            'avatar' => Yun::t('user', 'Avatar'),
            'unconfirmed_email' => Yun::t('user', 'Unconfirmed Email'),
            'unconfirmed_mobile' => Yun::t('user', 'Unconfirmed Mobile'),
            'registration_ip' => Yun::t('user', 'Registration Ip'),
            'flags' => Yun::t('user', 'Flags'),
            'email_confirmed_at' => Yun::t('user', 'Email Confirmed At'),
            'mobile_confirmed_at' => Yun::t('user', 'Mobile Confirmed At'),
            'blocked_at' => Yun::t('user', 'Blocked At'),
            'created_at' => Yun::t('user', 'Created At'),
            'updated_at' => Yun::t('user', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtra()
    {
        return $this->hasOne(UserExtra::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoginHistories()
    {
        return $this->hasMany(UserLoginHistory::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    /**
     * 返回所有已经连接的社交媒体账户
     * @return UserSocialAccount[] Connected accounts ($provider => $account)
     */
    public function getSocialAccounts()
    {
        $connected = [];
        /** @var UserSocialAccount[] $accounts */
        $accounts = $this->hasMany(UserSocialAccount::className(), ['user_id' => 'id'])->all();
        /**
         * @var UserSocialAccount $account
         */
        foreach ($accounts as $account) {
            $connected[$account->provider] = $account;
        }

        return $connected;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('{{%user_tag}}', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(UserToken::className(), ['user_id' => 'id']);
    }

    /**
     * 获取头像Url
     * @param string $size
     * @return string
     */
    public function getAvatar($size = self::AVATAR_MIDDLE)
    {
        $size = in_array($size, [self::AVATAR_BIG, self::AVATAR_MIDDLE, self::AVATAR_SMALL]) ? $size : self::AVATAR_BIG;
        if ($this->getIsAvatar()) {
            $avatarFileName = "_avatar_{$size}.jpg";
            return $this->getAvatarUrl($this->id) . $avatarFileName . '?_t=' . $this->updated_at;
        } else {
            $avatarUrl = "/img/no_avatar_{$size}.gif";
            if (Yun::getAlias('@webroot', false)) {
                $baseUrl = UserAsset::register(Yun::$app->view)->baseUrl;
                return Url::to($baseUrl . $avatarUrl, true);
            } else {
                return '';
            }
        }
    }

    /**
     * 设置用户资料
     * @param UserProfile $profile
     */
    public function setProfile(UserProfile $profile)
    {
        $this->_profile = $profile;
    }

    /**
     * 设置用户延伸资料
     * @param UserExtra $extra
     */
    public function setExtra($extra)
    {
        $this->_extra = $extra;
    }

    /**
     * 设置Email已经验证
     * @return bool
     */
    public function setEmailConfirm()
    {
        return (bool)$this->updateAttributes(['email_confirmed_at' => time()]);
    }

    /**
     * 设置手机号已经验证
     * @return bool
     */
    public function setMobileConfirm()
    {
        return (bool)$this->updateAttributes(['mobile_confirmed_at' => time()]);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * 通过登陆邮箱或手机号获取用户
     * @param string $emailOrMobile
     * @return User|null
     */
    public static function findByEmailOrMobile($emailOrMobile)
    {
        if (filter_var($emailOrMobile, FILTER_VALIDATE_EMAIL)) {
            return static::findByEmail($emailOrMobile);
        } else if (preg_match(self::$mobileRegexp, $emailOrMobile)) {
            return static::findByMobile($emailOrMobile);
        }
        return null;
    }

    /**
     * 通过邮箱获取用户
     * @param string $email 邮箱
     * @return null|static
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * 通过手机号获取用户
     * @param string $mobile
     * @return static
     */
    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile]);
    }

    /**
     * 通过用户名获取用户
     * @param string $username 用户标识
     * @return null|static
     */
    public static function findModelByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * 获取auth_key
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * 验证密码
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yun::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * 验证AuthKey
     * @param string $authKey
     * @return boolean
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 创建 "记住我" 身份验证Key
     * @return void
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yun::$app->security->generateRandomString();
    }

    /**
     * 创建 "记住我" 身份验证Key
     * @return void
     * @throws \yii\base\Exception
     */
    public function generateAccessToken()
    {
        $this->access_token = Yun::$app->security->generateRandomString();
    }

    /**
     * 随机生成一个用户名
     */
    public function generateUsername()
    {
        if ($this->email) {
            $this->username = explode('@', $this->email)[0];
            if ($this->validate(['username'])) {
                return $this->username;
            }
        } else if ($this->nickname) {
            $this->username = Inflector::slug($this->nickname, '');
            if ($this->validate(['username'])) {
                return $this->username;
            }
        }
        // generate name like "user1", "user2", etc...
        while (!$this->validate(['username'])) {
            $row = (new Query())->from('{{%user}}')->select('MAX(id) as id')->one();
            $this->username = 'user' . ++$row['id'];
        }
        return $this->username;
    }

    /**
     * 重置密码
     *
     * @param string $password
     *
     * @return boolean
     * @throws \yii\base\Exception
     */
    public function resetPassword($password)
    {
        return (bool)$this->updateAttributes(['password_hash' => PasswordHelper::hash($password)]);
    }

    /**
     * 锁定用户
     * @return boolean
     * @throws \yii\base\Exception
     */
    public function block()
    {
        return (bool)$this->updateAttributes(['blocked_at' => time(), 'auth_key' => Yun::$app->security->generateRandomString()]);
    }

    /**
     * 解除用户锁定
     * @return boolean
     */
    public function unblock()
    {
        return (bool)$this->updateAttributes(['blocked_at' => null]);
    }

    /**
     * 返回用户是否已经锁定
     * @return boolean Whether the user is blocked or not.
     */
    public function getIsBlocked()
    {
        return $this->blocked_at != null;
    }

    /**
     * 返回用户是否有头像
     * @return boolean Whether the user is blocked or not.
     */
    public function getIsAvatar()
    {
        return $this->avatar != 0;
    }

    /**
     * 返回用户邮箱是否已经激活
     * @return boolean Whether the user is confirmed or not.
     */
    public function getIsEmailConfirmed()
    {
        return $this->email_confirmed_at != null;
    }

    /**
     * 返回用户手机是否已经激活
     * @return boolean Whether the user is confirmed or not.
     */
    public function getIsMobileConfirmed()
    {
        return $this->mobile_confirmed_at != null;
    }

    /**
     * 电子邮件激活
     *
     * @param string $code Confirmation code.
     *
     * @return boolean
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function attemptConfirmation($code)
    {
        /** @var UserToken $token */
        $token = UserToken::findOne(['user_id' => $this->id, 'code' => $code, 'type' => UserToken::TYPE_CONFIRMATION]);
        if ($token instanceof UserToken && !$token->isExpired) {
            $token->delete();
            if (($success = $this->setEmailConfirm())) {
                Yun::$app->user->login($this, $this->getSetting('rememberFor'));
                $message = Yun::t('user', 'Thank you, registration is now complete.');
            } else {
                $message = Yun::t('user', 'Something went wrong and your account has not been confirmed.');
            }
        } else {
            $success = false;
            $message = Yun::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.');
        }
        Yun::$app->session->setFlash($success ? 'success' : 'danger', $message);
        return $success;
    }

    /**
     * 该方法将更新用户的电子邮件，如果`unconfirmed_email`字段为空将返回false,如果该邮件已经有人使用了将返回false; 否则返回true
     *
     * @param string $code
     *
     * @return boolean
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function attemptEmailChange($code)
    {
        /** @var UserToken $token */
        $token = UserToken::find()->where(['user_id' => $this->id, 'code' => $code])->andWhere(['in', 'type', [
            UserToken::TYPE_CONFIRM_NEW_EMAIL,
            UserToken::TYPE_CONFIRM_OLD_EMAIL
        ]])->one();
        if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
            Yun::$app->session->setFlash('danger', Yun::t('user', 'Your confirmation token is invalid or expired'));
            return false;
        } else {
            $token->delete();
            if (empty($this->unconfirmed_email)) {
                Yun::$app->session->setFlash('danger', Yun::t('user', 'An error occurred processing your request'));
                return false;
            } elseif (static::find()->where(['email' => $this->unconfirmed_email])->exists() == false) {
                if ($this->getSetting('emailChangeStrategy') == Settings::STRATEGY_SECURE) {
                    switch ($token->type) {
                        case UserToken::TYPE_CONFIRM_NEW_EMAIL:
                            $this->flags |= self::NEW_EMAIL_CONFIRMED;
                            Yun::$app->session->setFlash('success', Yun::t('user', 'Awesome, almost there. Now you need to click the confirmation link sent to your old email address'));
                            break;
                        case UserToken::TYPE_CONFIRM_OLD_EMAIL:
                            $this->flags |= self::OLD_EMAIL_CONFIRMED;
                            Yun::$app->session->setFlash('success', Yii::t('user', 'Awesome, almost there. Now you need to click the confirmation link sent to your new email address'));
                            break;
                    }
                }
                if ($this->getSetting('emailChangeStrategy') == Settings::STRATEGY_DEFAULT || ($this->flags & self::NEW_EMAIL_CONFIRMED && $this->flags & self::OLD_EMAIL_CONFIRMED)) {
                    $this->email = $this->unconfirmed_email;
                    $this->unconfirmed_email = null;
                    Yun::$app->session->setFlash('success', Yun::t('user', 'Your email address has been changed'));
                }
                $this->save(false);
                return true;
            }
            return false;
        }
    }

    /**
     * 此方法用于注册新用户帐户。 如果 enableConfirmation 设置为true，则此方法
     * 将生成新的确认令牌，并使用邮件发送给用户。
     *
     * @return boolean
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }
        $this->password = $this->getSetting('enableGeneratingPassword') ? PasswordHelper::generate(8) : $this->password;
        if ($this->scenario == self::SCENARIO_EMAIL_REGISTER) {
            $this->email_confirmed_at = $this->getSetting('enableConfirmation') ? null : time();
        }
        $this->trigger(self::BEFORE_REGISTER);
        if (!$this->save()) {
            return false;
        }
        if ($this->getSetting('enableConfirmation') && !empty($this->email)) {
            /** @var UserToken $token */
            $token = new UserToken(['type' => UserToken::TYPE_CONFIRMATION]);
            $token->link('user', $this);
            $this->sendMessage($this->email, Yun::t('user', 'Welcome to {0}', Yii::$app->name), 'welcome', ['user' => $this, 'token' => isset($token) ? $token : null, 'module' => $this->module, 'showPassword' => false]);
        } else {
            Yun::$app->user->login($this, $this->getSetting('rememberFor'));
        }
        $this->trigger(self::AFTER_REGISTER);
        return true;
    }

    /**
     * 创建新用户帐户。 如果用户不提供密码，则会生成密码。
     *
     * @return boolean
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }
        $this->password = $this->password == null ? PasswordHelper::generate(8) : $this->password;
        $this->trigger(self::BEFORE_CREATE);
        if (!$this->save()) {
            return false;
        }
        $this->sendMessage($this->email, Yun::t('user', 'Welcome to {0}', Yun::$app->name), 'welcome', ['user' => $this, 'token' => null, 'module' => $this->module, 'showPassword' => true]);
        $this->trigger(self::AFTER_CREATE);
        return true;
    }

//    public function afterFind()
//    {
//        parent::afterFind();
//        // ...custom code here...
//    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->generateAccessToken();
            $this->generateAuthKey();
            if (Yun::$app instanceof WebApplication) {
                $this->registration_ip = Yun::$app->request->getUserIP();
            }
            if ($this->username == null) {
                $this->username = $this->generateUsername();
            }
        }
        if (!empty($this->password)) {
            $this->password_hash = PasswordHelper::hash($this->password);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            if ($this->_profile == null) {
                $this->_profile = new UserProfile();
            }
            $this->_profile->link('user', $this);

            if ($this->_extra == null) {
                $this->_extra = new UserExtra();
            }
            $this->_extra->link('user', $this);
        }
    }

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
     * Returns the maximum number of allowed requests and the window size.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     * and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action)
    {
        $rateLimit = $this->getSetting('requestRateLimit', 60);
        return [$rateLimit, 60];
    }

    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action)
    {
        $allowance = Yun::$app->cache->GET($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance');
        $allowanceUpdatedAt = Yun::$app->cache->GET($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance_update_at');
        if ($allowance && $allowanceUpdatedAt) {
            return [$allowance, $allowanceUpdatedAt];
        } else {
            return [$this->getSetting('requestRateLimit', 60), time()];
        }
    }

    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param int $allowance the number of allowed requests remaining.
     * @param int $timestamp the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        Yun::$app->cache->set($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance', $allowance, 60);
        Yun::$app->cache->set($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance_update_at', $timestamp, 60);
    }
}
