<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs = array(
    Yii::t('nav', 'Users'),
);

$this->menu = array(
    array('label' => 'List User', 'url' => array('index'), 'active' => true),
    array('label' => 'Create User', 'url' => array('create')),
    array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2><?php
  echo Yii::t('nav', 'Users');

  $this->widget('ActionsWidget', array(
      'data' => $model,
      'updateButtonVisible' => 'false',
      'deleteButtonVisible' => 'false',
  ));
  ?>
</h2>

<?php
$resize = array(
    'resize' => array(
        'width' => Yii::app()->params['imageDimension']['maxWidthThumb'],
//        'height' => Yii::app()->params['imageDimension']['maxHeightThumb']
    )
);

$this->beginWidget('CActiveForm', array(
    'id' => 'user-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'User.export.{date}', array('{date}' => date('Ymd')));

$this->widget('ext.EExcelView', array(
    'title' => $title,
    'filename' => $title,
    'selectableRows' => 2,
    'id' => 'user-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            'header' => Yii::t('attributes', 'User.avatar'),
            'type' => 'html',
            'filter' => false,
            'sortable' => false,
            'value' => function ($data) use ($resize) {
              $gender = $data->gender === 'F' ? 'F' : 'M';
              $avatar = $data->avatar ? Yii::app()->params['avatarPath'] . $data->avatar : Yii::app()->params['avatarPath'] . 'default_avatar_' . $gender . '.jpg';
              return CHtml::link(Yii::app()->easyImage->thumbOf($avatar, $resize), array('view', 'id' => $data->id));
            },
                    'htmlOptions' => array(
                        'style' => 'width:' . Yii::app()->params['imageDimension']['maxWidthThumb'] * 1.1 . 'px;text-align:center;'
                    )
                ),
                array(
                    'name' => 'calc_name',
                    'type' => 'html',
                    'value' => 'CHtml::link($data->calc_name, array("view", "id" => $data->id));'
                ),
                'username',
                'email:email',
                array(
                    'name' => 'role',
                    'type' => 'html',
                    'sortable' => false,
                    'value' => function ($data) {
                      $roles = Yii::app()->authManager->getRoles($data->id);
                      $out = array();
                      foreach ($roles as $role)
                        $out[] = $role->name;
                      return nl2br(implode(PHP_EOL, $out));
                    },
                        ), /*
                          array(
                          'name' => 'project',
                          'type' => 'html',
                          'sortable' => false,
                          'value' => function ($data) {
                          $uCriteria = new CDbCriteria();
                          $uCriteria->together = true;
                          $uCriteria->with = array('project' => array('select' => 'name', 'alias' => 'p', 'scope' => 'open'));
                          $uCriteria->compare('t.user_id', $data->id);
                          $uProjects = UserProject::model()->findAll($uCriteria);
                          $out = array();
                          foreach ($uProjects as $uProject)
                          array_push($out, $uProject->project->name);
                          return nl2br(implode(PHP_EOL, $out));
                          },
                          ), */
                        array(
                            'name' => 'active',
                            'type' => 'boolean',
                            'filter' => array(Yii::app()->format->boolean(0), Yii::app()->format->boolean(1)),
                            'htmlOptions' => array('class' => 'col-date')
                        ),
                        array(
                            'class' => 'ext.myGridView.MyButtonColumn',
                            'buttons' => array(
                                'update' => array('visible' => 'Yii::app()->user->checkAccess("updateUser")'),
                                'delete' => array('visible' => 'Yii::app()->user->checkAccess("deleteUser")'),
                            ),
                        )
                    ),
                ));

                $this->endWidget();
                