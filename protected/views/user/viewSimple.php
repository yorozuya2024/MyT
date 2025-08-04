<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs = array(
    $model->username,
);
?>

<h1>
  <?php echo $model->username; ?>
</h1>

<?php
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
        'phone',
        'mobile',
        'email:email',
        array(
            'name' => 'avatar',
            'type' => 'image',
            'value' => $avatar
        )
    ),
));

