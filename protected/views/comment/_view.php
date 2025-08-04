<?php
/* @var $this CommentController */
/* @var $data Comment */

$avatar = $data->creator->avatar ? 
		  Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . $data->creator->avatar : 
		  Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . 'default_avatar_' . $data->creator->gender . '.jpg';
		  
$parser = new CHtmlPurifier();

$currentUser = Yii::app()->user;

$imgEdit = CHtml::image(Yii::app()->baseUrl . '/images/actions/database_edit.png', 'Edit');
$imgDelete = CHtml::image(Yii::app()->baseUrl . '/images/actions/database_delete.png', 'Delete');

$canModify = $currentUser->getId() == $data->created_by || $currentUser->checkAccess('adminConfig');
?>

<div class="view">
	<div style="float: left; margin: 5px;">
		<?php echo CHtml::image($avatar, $data->creator->calc_name, array('height' => 48, 'width' => 48) ); ?>
	</div>
	<div id="comment-<?php echo $data->id; ?>" style="float: left; width: 90%">
		<h3><?php echo $data->creator->calc_name; ?></h3>
		<?php echo $parser->purify($data->body); ?>
		<em><?php echo Yii::app()->dateFormatter->formatDateTime($data->created, 'medium', 'medium'); ?></em>
	</div>
	<div style="float: right;">
		<?php echo !$canModify ? '' : CHtml::link($imgEdit, array('comment/update', 'id' => $data->id)); ?>
		<?php echo !$canModify ? '' : CHtml::link($imgDelete,
				 array('comment/delete','id'=>$data->id),
				 array('onclick' => 'return confirm(' . CJavaScript::encode(Yii::t('zii', 'Are you sure you want to delete this item?')) . ')',
						'id' => 'delete-'.$data->id));
	?>
	</div>
	
	<div style="clear: both;"></div>

</div>