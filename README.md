# cms

#### Composer 

```bash
composer install --prefer-source --optimize-autoloader -vvv
composer update --prefer-source --optimize-autoloader -vvv

composer install --prefer-dist --optimize-autoloader -vvv
composer update --prefer-dist --optimize-autoloader -vvv
```

#### 生成语言包

```bash
./yii message @yuncms/messages/config.php
```

##### 创建数据迁移文件(支持命名空间)

```
用Gii生成模型
php yii gii/model --ns=common\\models --modelClass=AdminLog --tableName=admin_log

./yii migrate/create yuncms\migrations\Create_user_extra_table

```
