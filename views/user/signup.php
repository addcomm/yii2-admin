<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \mdm\admin\models\form\Signup */
/* @var $roles array */

$this->title = Yii::t('rbac-admin', 'New')." ".Yii::t('rbac-admin', 'User');
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-body">
            <div class="site-signup">
                <p>Please fill out the following fields to create a new user:</p>
                <?= Html::errorSummary($model)?>
                <div class="row">
                    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                    <div class="col-lg-6">
                        <!--<?= $form->field($model, 'username') ?>-->
                        <?= $form->field($model, 'email') ?>
                        <?= $form->field($model, 'password')->passwordInput() ?>
                        <?= $form->field($model, 'role')->dropDownList($roles, ['prompt' => 'Select Role' ]); ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'name') ?>
                        <?= $form->field($model, 'surname') ?>
                        <?= $form->field($model, 'phone') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('rbac-admin', 'Create New'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</section>
