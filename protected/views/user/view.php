<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs = array(
    Yii::t('nav', 'Users') => array('index'),
    $model->username,
);

$this->menu = array(
    array('label' => 'List User', 'url' => array('index')),
    array('label' => 'Create User', 'url' => array('create')),
    array('label' => 'Manage User', 'url' => array('admin')),
    array('label' => 'Update User', 'url' => array('update', 'id' => $model->id)),
    array('label' => 'Delete User', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Are you sure you want to delete this item?')),
);
?>

<h1>
  <?php
  echo $model->username;

  $this->widget('ActionsWidget', array(
      'data' => $model
  ));
  ?>
</h1>

<?php
$uCriteria = new CDbCriteria();
$uCriteria->together = true;
$uCriteria->with = array('project' => array('select' => 'name', 'alias' => 'p', 'scope' => 'open'));
$uCriteria->compare('t.user_id', $model->id);
$uProjects = UserProject::model()->findAll($uCriteria);
$projectNames = array();
foreach ($uProjects as $uProject)
  array_push($projectNames, $uProject->project->name);

$roles = Yii::app()->authManager->getRoles($model->id);
$roleNames = array();
foreach ($roles as $role)
  array_push($roleNames, $role->name);

$gender = $model->gender === 'F' ? 'F' : 'M';
$avatar = $model->avatar ? Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . $model->avatar : Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . 'default_avatar_' . $gender . '.jpg';
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'calc_name',
        array(
            'name' => 'gender',
            'value' => $model->gender ? ($model->gender === 'M' ? Yii::t('constants', 'User.male') : Yii::t('constants', 'User.female')) : null
        ),
        'level',
        array(
            'name' => 'load_cost',
            'value' => Yii::app()->numberFormatter->formatCurrency($model->load_cost, 'EUR'),
        ),
        array(
            'name' => 'daily_hours',
            'value' => Yii::app()->numberFormatter->formatDecimal($model->daily_hours),
        ),
        'phone',
        'mobile',
        'email:email',
        array(
            'label' => Yii::t('attributes', 'User.project'),
            'type' => 'html',
            'value' => nl2br(implode(PHP_EOL, $projectNames)),
        ),
        array(
            'label' => Yii::t('attributes', 'User.role'),
            'type' => 'html',
            'value' => nl2br(implode(PHP_EOL, $roleNames)),
        ),
        array(
            'name' => 'avatar',
            'type' => 'image',
            'value' => $avatar
        )
    ),
));

