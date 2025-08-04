<?php Yii::app()->counter->refresh(); ?>
<?php $online = Yii::app()->counter->getOnline(); ?>
<?php echo Yii::t('app', 'Online'), ': ', count($online); ?>
<ul>
  <?php
  $onlineId = array();
  foreach ($online as $user)
    array_push($onlineId, $user['user_id']);
  $userCriteria = new CDbCriteria();
  $userCriteria->addInCondition('id', $onlineId);
  $userCriteria->select = array('id', 'username');
  $userCriteria->order = 'username';
  $users = User::model()->findAll($userCriteria);
  $userLinks = array();
  foreach ($users as $user)
    array_push($userLinks, CHtml::link($user->username, array('/user/view', 'id' => $user->id)));
  foreach ($userLinks as $userLink)
    echo '<li>', $userLink, '</li>';
  ?>
</ul>