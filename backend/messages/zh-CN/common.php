<?php
$message = yii\helpers\ArrayHelper::merge(
    require 'common/index.php',
    require 'common/menu.php',
    require 'common/site.php'
);
return $message;