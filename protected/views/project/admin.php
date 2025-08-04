<?php
/* @var $this ProjectController */
/* @var $model Project */

$source = Yii::app()->user->getState('Project') === 'All' ? 'indexAll' : 'index';
$label = Yii::app()->user->getState('Project') === 'All' ? 'All ' : 'My ';

$this->breadcrumbs = array(
    Yii::t('nav', $label . 'Projects') => array($source),
    Yii::t('nav', 'Manage'),
);
?>

<h1><?php echo Yii::t('nav', 'Manage Projects'); ?></h1>

<?php
$this->beginWidget('CActiveForm', array(
    'id' => 'project-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'Project.Task.all.export.{date}', array('{date}' => date('Ymd')));

//$this->widget('zii.widgets.grid.CGridView', array(
$this->widget('ext.EExcelView', array(
    'title' => $title,
    'filename' => $title,
    'selectableRows' => 2,
    'id' => 'project-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            'name' => 'name',
            'type' => 'html',
            'value' => function($data) {
              return CHtml::link($data->name, array('project/viewTasks', 'id' => $data->id));
            }
                ),
                array(
                    'name' => 'champion_id',
                    'filter' => CHtml::listData(User::model()->findAll(), 'id', 'username'),
                    'value' => '$data->champion ? $data->champion->username : null'
                ),
                array(
                    'name' => 'created_by',
                    'filter' => CHtml::listData(User::model()->findAll(), 'id', 'username'),
                    'value' => '$data->creator->username'
                ),
                array(
                    'header' => Yii::t('attributes', 'Project.openTasks'),
                    'value' => function ($data) {
                      return Task::model()->count(array('select' => 'id', 'condition' => 'par_project_id = :pid AND status = :open', 'params' => array('pid' => $data->id, 'open' => 0)));
                    }
                        ),
                        array(
                            'header' => Yii::t('attributes', 'Project.allTasks'),
                            'value' => function ($data) {
                              return Task::model()->count(array('select' => 'id', 'condition' => 'par_project_id = :pid', 'params' => array('pid' => $data->id)));
                            }
                                ),
                                array(
                                    'name' => 'par_project_id',
                                    'filter' => CHtml::listData(Project::model()->findAll(), 'id', 'name'),
                                    'value' => '$data->parProject ? $data->parProject->name : null'
                                ),
                                'client',
                                array(
                                    'class' => 'ext.myGridView.MyButtonColumn',
                                    'buttons' => array(
                                        'update' => array('visible' => 'Yii::app()->user->checkAccess("updateProject")'),
                                        'delete' => array('visible' => 'Yii::app()->user->checkAccess("deleteProject")'),
                                    ),
                                ),
                                array(
                                    'class' => 'CButtonColumn',
                                    'template' => '{email}',
                                    'header' => Yii::t('app', 'Email'),
                                    'buttons' => array(
                                        'email' => array(
                                            'label' => Yii::t('app', 'Email'),
                                            'imageUrl' => Yii::app()->baseUrl . '/images/actions/mail_icon.png',
                                            'url' => function ($data) {
                                              $emails = array();
                                              foreach ($data->users as $user)
                                                array_push($emails, $user->email);
                                              $cc = $data->champion ? $data->champion->email : '';
                                              return 'mailto:' . implode(';', $emails) . '?subject=' . $data->name . '&cc=' . $cc;
                                            },
                                                )
                                            ),
                                        ),
                                    ),
                                ));

                                $this->endWidget();
