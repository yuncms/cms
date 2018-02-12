# <?= $generator->title ?>

<?= $generator->description ?>

[![Latest Stable Version](https://poser.pugx.org/<?= $generator->vendorName ?>/<?= $generator->packageName ?>/v/stable.png)](https://packagist.org/packages/<?= $generator->vendorName ?>/<?= $generator->packageName ?>)
[![Total Downloads](https://poser.pugx.org/<?= $generator->vendorName ?>/<?= $generator->packageName ?>/downloads.png)](https://packagist.org/packages/<?= $generator->vendorName ?>/<?= $generator->packageName ?>)
[![Build Status](https://img.shields.io/travis/<?= $generator->vendorName ?>/<?= $generator->packageName ?>.svg)](http://travis-ci.org/<?= $generator->vendorName ?>/<?= $generator->packageName ?>)
[![License](https://poser.pugx.org/<?= $generator->vendorName ?>/<?= $generator->packageName ?>/license.svg)](https://packagist.org/packages/<?= $generator->vendorName ?>/<?= $generator->packageName ?>)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist <?= $generator->vendorName ?>/<?= $generator->packageName ?> "*"
```

or add

```
"<?= $generator->vendorName ?>/<?= $generator->packageName ?>": "*"
```

to the require section of your `composer.json` file.


## Usage

Once the extension is installed, simply use it in your code by  :

```php
<?= "<?= \\{$generator->namespace}AutoloadExample::widget(); ?>" ?>
```