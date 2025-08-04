<?php
/*              */
/* 新規会員登録 */
/*              */
?>

<h1>新規会員登録</h1>

<?php if (Yii::app()->user->hasFlash('success')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php $form = $this->beginWidget('CActiveForm', array('id' => 'register-form')); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'username'); ?>
        <?php echo $form->textField($model, 'username'); ?>
        <?php echo $form->error($model, 'username'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'email'); ?>
        <?php echo $form->textField($model, 'email'); ?>
        <?php echo $form->error($model, 'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'password'); ?>
        <?php echo $form->passwordField($model, 'password'); ?>
        <?php echo $form->error($model, 'password'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'password_repeat'); ?>
        <?php echo $form->passwordField($model, 'password_repeat'); ?>
        <?php echo $form->error($model, 'password_repeat'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'gender'); ?>
        <?php echo $form->dropDownList($model, 'gender', array('M' => '男性', 'F' => '女性')); ?>
        <?php echo $form->error($model, 'gender'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('登録'); ?>
    </div>

<?php $this->endWidget(); ?>

