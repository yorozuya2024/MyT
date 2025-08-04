<?php
/* @var $this CommentController */
/* @var $model Comment */
/* @var $form CActiveForm */
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js', CClientScript::POS_HEAD);

$action = $handler ? array('comment/create') : array('comment/update', 'id' => $model->id);

?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'comment-form',
	'action' => $action,
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); 
		  echo $form->hiddenField($model,'entity',array('value' => $entity));
		  echo $form->hiddenField($model,'entity_id',array('value' => $entityId));
    ?>

	<div class="row">
		<div style="float: left; margin: 5px;">
			<?php echo CHtml::image(Yii::app()->user->avatar, Yii::app()->user->getName(), array('height' => 48, 'width' => 48) ); ?>
		</div>
		<div style="float: left; width: 95%">
			<?php echo $form->textArea($model,'body',array('rows'=>3, 'style' => 'width: 100%', 'placeholder' => Yii::t('app', 'Comment.write') )); ?>
			<div class="row buttons" style="text-align: right; display: none;">
				<?php //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save');
					echo !$handler ? CHtml::submitButton($model->isNewRecord ? Yii::t('app', 'Form.create') : 
															Yii::t('app', 'Form.save')) : 
									CHtml::ajaxSubmitButton(Yii::t('app', 'Form.save'), $action, 
									array('success' => 'function(data) { ' . $handler . ' 
										$("#removeCKEditor").click();
								}')); ?>
				<?php echo CHtml::resetButton(Yii::t('app', 'Form.cancel'), array('id' => 'removeCKEditor')); ?>
			</div>
		</div>
		<?php echo $form->error($model,'body'); ?>
		<div style="clear: both"></div>
	</div>
	
	<?php Yii::app()->clientScript->registerScript('rich-editor', '
	$("#Comment_body").click( function()
	{
		CKEDITOR.replace("Comment_body", {
			customConfig: "config.js",
			width: "100%",
			toolbarGroups: [
				{"name":"basicstyles","groups":["basicstyles"]},
				{"name":"links","groups":["links"]},
				{"name":"paragraph","groups":["list","blocks"]},
				{"name":"document","groups":["mode"]},
				{"name":"insert","groups":["insert"]},
				{"name":"styles","groups":["styles"]}
			]
		});
		
		CKEDITOR.instances.Comment_body.on(\'key\', function(event) {  });
		
		$(".row .buttons").show();
	});
	$("#yt0").click(function(){
		console.log("save invocato");
		CKEDITOR.instances.Comment_body.updateElement();
	});
	$("#removeCKEditor").click( function() {
		' . ($handler ? '' : 'window.history.back();') . '
		CKEDITOR.instances.Comment_body.destroy();
		$(".row .buttons").hide();
	});
	' . ($handler ? '' : '$("#Comment_body").click();'));
	
	?>

<?php $this->endWidget(); ?>