<?php
/* @var $this ProjectController */
/* @var $model Project */
/* @var $tasks Task */

$source = Navigator::getProjectType() === 'all' ? 'indexAll' : 'index';
$label = ucfirst(Navigator::getProjectType());
Navigator::setProjectId($model->id);

$this->breadcrumbs = array(
    Yii::t('nav', $label . ' Projects') => array($source),
    $model->name,
);

$this->menu = array(
    array('label' => 'List Project', 'url' => array('index')),
    array('label' => 'Create Project', 'url' => array('create')),
    array('label' => 'Manage Projects', 'url' => array('admin')),
    array('label' => 'Update Project', 'url' => array('update', 'id' => $model->id)),
    array('label' => 'Delete Project', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id),
            'confirm' => 'Are you sure you want to delete this item?')),
    array('label' => 'View Project', 'url' => array('project/view', 'id' => $model->id)),
    array('label' => 'Tasks', 'url' => array('taskProject/index', 'projectId' => $model->id), 'items' => array(
            array('label' => 'Create Task', 'url' => array('taskProject/create', 'projectId' => $model->id)),
        )),
    array('label' => 'Users', 'url' => array('userProject/indexByProject', 'projectId' => $model->id), 'items' => array(
            array('label' => 'Manage Users', 'url' => array('userProject/adminByProject', 'projectId' => $model->id)),
            array('label' => 'Associate New User', 'url' => array('userProject/createByProject', 'projectId' => $model->id)),
        )
    ),
);
?>

<h1>
  <?php echo $model->name; ?>
  <?php echo CHtml::link('+', '#', array('id' => 'Project_details')); ?>
  <?php
  Yii::app()->clientScript->registerScript('Project_details', '
        $("#project-grid").hide();
        var plus = true;
        $("#Project_details").click(function(){
            $("#project-grid").slideToggle("fast");
            $(this).text(plus ? "-" : "+");
            plus = !plus;
        }).css("text-decoration", "none");
        ', CClientScript::POS_END
  );
  ?>
</h1>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'project-grid',
    'dataProvider' => $model->search(),
    'filter' => null,
    'enablePagination' => false,
    'enableSorting' => false,
    'selectableRows' => 0,
    'template' => '{items}',
    'columns' => array(
        array(
            'name' => 'name',
            'type' => 'html',
            'value' => 'CHtml::link($data->name, array("view", "id" => $data->id))'
        ),
        'client',
        array(
            'name' => 'champion.username',
            'header' => $model->getAttributeLabel('champion_id')
        ),
        array(
            'name' => 'status',
            'value' => '$data->getStatus()'
        ),
        array(
            'name' => 'parProject.name',
            'header' => $model->getAttributeLabel('par_project_id')
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
        ?>

        <hr style="height:1px;margin:1em 0;background-color:#D3E7EC;" />

        <h2>Tasks
          <?php
          $this->widget('ActionsWidget', array(
              'data' => $tasks,
              'createButtonUrl' => 'array("task/create", "project_id" => ' . $model->id . ')',
              'updateButtonVisible' => 'false',
              'deleteButtonVisible' => 'false',
          ));
          ?>
        </h2>
        <?php
        $this->renderPartial('/task/_adminByProject', array(
            'project' => $model,
            'tasks' => $tasks
        ));
        ?>