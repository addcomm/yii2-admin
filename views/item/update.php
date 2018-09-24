<?php

use backend\assets\AppAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = Yii::t('rbac-admin', 'Update ' . $labels['Item']) . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('rbac-admin', 'Update');
AppAsset::register($this);

?>

<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-body">
            <div class="auth-item-update">
                <?=
                $this->render('_form', [
                    'model' => $model,
                    'modelRoutes' => $modelRoutes,
                ]);
                ?>
            </div>
        </div>
    </div>
</section>