<?php
/* @var $this ProjectController */
/* @var $model Project */

Navigator::clear();

$this->breadcrumbs = array(
    Yii::t('nav', 'My Projects'),
);

$this->menu = array(
    array('label' => 'List Project', 'url' => array('index'), 'active' => true),
    array('label' => 'Create Project', 'url' => array('create')),
    array('label' => 'Manage Project', 'url' => array('admin')),
);
?>

<h2><?php
  echo Yii::t('nav', 'My Projects');

  $this->widget('ActionsWidget', array(
      'data' => $model,
      'updateButtonVisible' => 'false',
      'deleteButtonVisible' => 'false',
  ));
  ?>
</h2>

<?php
$pCriteria = new CDbCriteria();
$pCriteria->select = array('id', 'name');
$pCriteria->addCondition('EXISTS (SELECT id FROM {{project}} WHERE par_project_id = t.id)');
$pCriteria->order = 'name';

$this->beginWidget('CActiveForm', array(
    'id' => 'project-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'Project.Task.my.export.{date}', array('{date}' => date('Ymd')));

$this->widget('ext.HierProjectExcelView', array(
    'title' => $title,
    'filename' => $title,
    'selectableRows' => 2,
    'id' => 'project-grid',
    'dataProvider' => $model->searchMyHierarchical(),
    'filter' => $model,
    'afterAjaxUpdate' => "function(id, data){
	    jQuery('#Project_status').multiselect({
            selectedList: 2
        });
        $('#' + id + ' .items').treeTable({treeColumn:1});
    }",
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
                    'filter' => CHtml::listData(User::model()->findAll(array('order' => 'username')), 'id', 'username'),
                    'value' => '$data->champion ? $data->champion->username : null'
                ),
                array(
                    'header' => Yii::t('attributes', 'Project.openTasks'),
                    'value' => function ($data) {
                      return Task::model()->public()->count(array('select' => 'id', 'condition' => 'par_project_id = :pid AND status IN (:open, :wip)', 'params' => array('pid' => $data->id, 'open' => 1, 'wip' => 2)));
                    }
                        ),
                        array(
                            'header' => Yii::t('attributes', 'Project.allTasks'),
                            'value' => function ($data) {
                              return Task::model()->public()->count(array('select' => 'id', 'condition' => 'par_project_id = :pid', 'params' => array('pid' => $data->id)));
                            }
                                ),
                                array(
                                    'name' => 'par_project_id',
                                    'filter' => CHtml::listData(Project::model()->findAllHierarchical($pCriteria), 'id', function($project) {
                                              return str_pad($project->name, strlen($project->name) + 2 * $project->level, '- ', STR_PAD_LEFT);
                                            }),
                                    'value' => '$data->parProject ? $data->parProject->name : null'
                                ),
                                'client',
                                array(
                                    'name' => 'status',
                                    'filter' => CHtml::activeDropDownList($model, 'status', Project::model()->getStatusList(), array('multiple' => true, 'style' => 'display:none')),
                                    'value' => '$data->getStatus()',
                                ),
                                array(
                                    'class' => 'ext.myGridView.MyButtonColumn',
                                    'buttons' => array(
                                        'update' => array('visible' => 'Yii::app()->user->checkAccess("updateProject")'),
                                        'delete' => array('visible' => 'Yii::app()->user->checkAccess("deleteProject")'),
                                    ),
                                    'deleteConfirmation' => "js:(parseInt($(this).parent().parent().children().eq(3).text()) > 0 ? '" . Yii::t('app', 'Project.delete.confirmation') . "<br />' : '') + '" . Yii::t('zii', 'Are you sure you want to delete this item?') . "'",
									'afterDelete' => 'function(link,success,data){ if( data != \'\' ) { alert(data); } }'
                                ),
                                array(
                                    'class' => 'CButtonColumn',
                                    'template' => '{email}',
                                    'header' => Yii::t('app', 'Email'),
                                    'buttons' => array(
                                        'email' => array(
                                            'header' => Yii::t('app', 'Email'),
                                            'imageUrl' => Yii::app()->baseUrl . '/images/actions/mail_icon.png',
                                            'url' => function ($data) {
                                              $emails = array();
                                              foreach ($data->enrolledUsers as $user)
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

                                $multiselectFolder = Yii::app()->baseUrl . '/js/multiselect/';
                                Yii::app()->clientScript->registerScriptFile('jquery-ui.min.js');
                                Yii::app()->clientScript->registerCSSFile('jquery-ui.css');
                                Yii::app()->clientScript->registerScriptFile($multiselectFolder . 'jquery.multiselect.min.js', CClientScript::POS_END);
                                Yii::app()->clientScript->registerCSSFile($multiselectFolder . 'jquery.multiselect.css');
                                Yii::app()->clientScript->registerScript('multiselect.filter', '
    $("#Project_status").multiselect({
        selectedList: 2
    });
', CClientScript::POS_READY);
