<?php

use backend\assets\AppAsset;
use backend\components\grid;
use yii\helpers\Html;
use yii\grid\GridView;
use mdm\admin\components\RouteRule;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\AuthItem */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = Yii::t('rbac-admin', $labels['Items']);
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

$rules = array_keys(Yii::$app->getAuthManager()->getRules());
$rules = array_combine($rules, $rules);
unset($rules[RouteRule::RULE_NAME]);

?>

<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-body">
            <div class="role-index">
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'name',
                            'label' => Yii::t('rbac-admin', 'Name'),
                        ],
                        [
                            'attribute' => 'ruleName',
                            'label' => Yii::t('rbac-admin', 'Rule Name'),
                            'filter' => $rules
                        ],
                        [
                            'attribute' => 'description',
                            'label' => Yii::t('rbac-admin', 'Description'),
                        ],
                        ['class' => 'backend\components\grid\CActionColumn',],
                    ],
                ])
                ?>

            </div>
        </div>
    </div>
</section>
