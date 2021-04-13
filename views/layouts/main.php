<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $content string */

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <header></header>
    <?= $content ?>
    <footer></footer>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
