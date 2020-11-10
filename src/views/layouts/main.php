<?php

/**
 * @var \src\components\View $this
 * @var string $content
 */

declare(strict_types = 1);

use yii\helpers\Html;
use src\applications\frontend\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

$session = Yii::$app->getSession();

$this->beginPage();

?><!DOCTYPE html>
<html lang="<?=$this->getDocumentLanguage()?>">
<head>
    <meta charset="UTF-8"/>
    <?=Html::csrfMetaTags()?>
    <title><?=$this->title?></title>
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <meta name="format-detection" content="telephone=no">
    <?php $this->head();?>
</head>
<body>
<?php $this->beginBody()?>

<div class="wrapper">
    <?=$content?>
</div>

<?php $this->endBody();?>

</body>
</html>
<?php $this->endPage();