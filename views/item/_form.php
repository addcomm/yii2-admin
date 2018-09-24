<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mdm\admin\components\RouteRule;
use mdm\admin\AutocompleteAsset;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$rules = Yii::$app->getAuthManager()->getRules();
unset($rules[RouteRule::RULE_NAME]);
$source = Json::htmlEncode(array_keys($rules));

$js = <<<JS
    $('#rule_name').autocomplete({
        source: $source,
    });
JS;
AutocompleteAsset::register($this);
$this->registerJs($js);
?>

<div class="auth-item-form">
    <?php $form = ActiveForm::begin(['id' => 'item-form']); ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label><?=Yii::t('backend/role/update','LABEL_ROLE_NAME')?></label>
                <input class="form-control" type="text" readonly value="<?=$model->name?>">
            </div>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'description')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" width="100%" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th><?=Yii::t('backend/role/update','ROUTE')?></th>
                        <?php foreach(Yii::$app->params['RIGHTS'] as $right) { ?>
                         <th><?=strtoupper($right)?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($modelRoutes as $route =>$rights) { ?>
                    <tr>
                        <td><?=$route?></td>
                        <?php foreach(Yii::$app->params['RIGHTS'] as $right) { ?>
                            <td><input type="checkbox" name="Rights[<?=$route?>][<?=$right?>]" <?php if(strpos($rights,$right) !== FALSE) { ?>  checked="checked" <?php } ?> class="checkbox"></td>
                        <?php } ?>
                    </tr>
                <?php }  ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="form-group">
        <?php
        echo Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            'name' => 'submit-button'])
        ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
