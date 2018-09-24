<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\grid\GridView;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $searchModel mdm\admin\models\searchs\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rbac-admin', 'Users');
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);


?>

<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-body">
            <div class="user-index">
                <p>
                    <?= Html::a(Yii::t('rbac-admin', 'New')." ".Yii::t('rbac-admin', 'User'), ['signup'], ['class' => 'btn btn-success']) ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'username',
                        'email:email',
                        'created_at:date',
                        [
                            'attribute' => 'status',
                            'value' => function($model) {
                                return $model->status == 0 ? 'Inactive' : 'Active';
                            },
                            'filter' => [
                                0 => 'Inactive',
                                10 => 'Active'
                            ]
                        ],
                        [
                            'class' => 'backend\components\grid\CActionColumn',
                            'template' => Helper::filterActionColumn(['view', 'edit','activate', 'delete']),
                            'buttons' => [
                                'edit' => function($url, $model) {
                                    if ( !Yii::$app->user->can(\common\components\CUser::ROLE_ADMIN) ) {
                                        return '';
                                    }
                                    $url = '/admin/assignment/view?id='.$model->id;
                                    $options = [
                                        'class' => 'btn btn-sm btn-link',
                                        'title' => Yii::t('rbac-admin', 'Edit'),
                                        'aria-label' => Yii::t('rbac-admin', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="fa fa-fw fa-pencil"></span>', $url, $options);
                                },
                                'activate' => function($url, $model) {
                                    if ($model->status == 10) {
                                        return '';
                                    }
                                    $options = [
                                        'title' => Yii::t('rbac-admin', 'Activate'),
                                        'aria-label' => Yii::t('rbac-admin', 'Activate'),
                                        'data-confirm' => Yii::t('rbac-admin', 'Are you sure you want to activate this user?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, $options);
                                }
                                ]
                            ],
                        ],
                    ]);
                    ?>
            </div>
        </div>
    </div>
</section>
