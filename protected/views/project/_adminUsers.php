<?php

/* @var $this ProjectController */
/* @var $model UserProject */
/* @var $project Project */
?>

<?php

$startDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $model,
    'attribute' => 'rollon_date',
    'key' => 'filter_S8nwIpvF_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$endDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $model,
    'attribute' => 'rolloff_date',
    'key' => 'filter_S8nwIpvF_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$resize = array(
    'resize' => array(
        'width' => Yii::app()->params['imageDimension']['maxWidthThumb'],
//        'height' => Yii::app()->params['imageDimension']['maxHeightThumb']
    )
);
$buttons = '';
if (Yii::app()->user->checkAccess('updateProject'))
  $buttons .= '{update} {delete}';

$this->beginWidget('CActiveForm', array(
    'id' => 'user-project-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'Project.User.export.{project}.{date}', array(
            '{project}' => $project->name,
            '{date}' => date('Ymd')
        ));

$this->widget('ext.EExcelView', array(
    'title' => $title,
    'filename' => $title,
    'selectableRows' => 2,
    'id' => 'user-project-grid',
    'dataProvider' => $model->searchByProject($project->id),
    'filter' => $model,
    'afterAjaxUpdate' => "function(id, data){
        {$startDateFilter->js}
        {$endDateFilter->js}
    }",
    'columns' => array(
        array(
            'header' => Yii::t('attributes', 'User.avatar'),
            'type' => 'html',
            'filter' => false,
            'sortable' => false,
            'value' => function ($data) use ($resize) {
              $gender = $data->user->gender === 'F' ? 'F' : 'M';
              $avatar = $data->user->avatar ? Yii::app()->params['avatarPath'] . $data->user->avatar : Yii::app()->params['avatarPath'] . 'default_avatar_' . $gender . '.jpg';
              return CHtml::link(Yii::app()->easyImage->thumbOf($avatar, $resize), array('user/view', 'id' => $data->user->id));
            },
                    'htmlOptions' => array(
                        'style' => 'width:' . Yii::app()->params['imageDimension']['maxWidthThumb'] * 1.1 . 'px;text-align:center;'
                    )
                ),
                array(
                    'name' => 'user_id',
                    'filter' => CHtml::listData(User::model()->active()->findAll(array('order' => 'username')), 'id', 'username'),
                    'value' => '$data->user->username'
                ),
                array(
                    'name' => 'user.calc_name',
                    'type' => 'html',
                    'filter' => CHtml::textField('User[calc_name]'),
                    'value' => 'CHtml::link($data->user->calc_name, array("user/view", "id" => $data->user->id));'
                ),
                array(
                    'name' => 'user.email',
                    'type' => 'email',
                    'filter' => CHtml::textField('User[email]')
                ),
                array(
                    'name' => 'user.level',
                    'filter' => CHtml::textField('User[level]'),
                ),
                array(
                    'name' => 'rollon_date',
                    'type' => 'date',
                    'filter' => $startDateFilter->content,
                    'htmlOptions' => array('class' => 'col-date'),
                ),
                array(
                    'name' => 'rolloff_date',
                    'type' => 'date',
                    'filter' => $endDateFilter->content,
                    'htmlOptions' => array('class' => 'col-date'),
                ),
                array(
                    'class' => 'ext.myGridView.MyButtonColumn',
                    'template' => $buttons,
                    'deleteButtonUrl' => 'array("userProject/delete", "id" => $data->id)',
                    'updateButtonUrl' => 'array("userProject/update", "id" => $data->id)',
                ),
            ),
        ));

        $this->endWidget();
        