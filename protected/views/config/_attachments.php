<?php
/* @var $this ConfigController */
/* @var $model ConfigForm */
?>

<div class="row">
  <?php echo $form->labelEx($model, 'attachments[enable]'); ?>
  <?php echo $form->checkBox($model, 'attachments[enable]'); ?>
  <?php echo $form->error($model, 'attachments[enable]'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'attachments[storage]'); ?>
  <?php
  echo $form->dropDownList($model, 'attachments[storage]', array(
      'local' => 'Local',
      'mega' => 'Mega',
  ));
  ?>
  <?php echo $form->error($model, 'attachments[storage]'); ?>
</div>

<div class="row" id="attachment-storage-local">
  <?php echo $form->labelEx($model, 'attachments[path]'); ?>
  <?php echo $form->textField($model, 'attachments[path]', array('size' => 50)); ?>
  <?php echo $form->error($model, 'attachments[path]'); ?>
</div>

<div class="row" id="attachment-storage-mega">
  <fieldset>
    <legend>Mega API Parameters</legend>
    <?php echo $form->labelEx($model, 'attachments[apiUser]'); ?>
    <?php echo $form->textField($model, 'attachments[apiUser]', array('size' => 50)); ?>
    <?php echo $form->error($model, 'attachments[apiUser]'); ?>
    <?php echo $form->labelEx($model, 'attachments[apiPassword]'); ?>
    <?php echo $form->textField($model, 'attachments[apiPassword]', array('size' => 50)); ?>
    <?php echo $form->error($model, 'attachments[apiPassword]'); ?>
    <?php echo $form->hiddenField($model, 'attachments[apiKey]'); ?>
    <?php echo $form->hiddenField($model, 'attachments[apiSID]'); ?>
    <div class="row">
      <?php
      echo CHtml::ajaxButton('Login', array('megaLogin'), array(
          'cache' => false,
          'data' => 'js:{u:$("#ConfigForm_attachments_apiUser").val(),p:$("#ConfigForm_attachments_apiPassword").val()}',
          'dataType' => 'json',
          'success' => 'js:function(data) {
                    console.log(data);
                    $("#attachment-mega-login-dlg-waiting").hide();
                    if (data.key.substring(0, 3) === "a:0") {
                        $("#attachment-mega-login-dlg-ko").show();
                    } else {
                        $("#attachment-mega-login-dlg-ok").show();
                        $("#ConfigForm_attachments_apiKey").val(data.key);
                        $("#ConfigForm_attachments_apiSID").val(data.sid);
                    }
                }',
          'error' => 'js:function(data) {
                    console.log(data.status);
                    console.log(data.statusText);
                    console.log(data.responseText);
                    $("#attachment-mega-login-dlg-waiting").hide();
                    $("#attachment-mega-login-dlg-error").text(data.statusText);
                }',
          'complete' => 'js:function() {
                    $("#attachment-mega-login-dlg").next().find("button").button("enable");
                }',
              ), array('id' => 'attachment-mega-login-btn'));
      ?>
      <span class="hint">May take a few minutes.</span>
    </div>
  </fieldset>
</div>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'attachment-mega-login-dlg',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => 'API Login',
        'autoOpen' => false,
        'modal' => true,
        'closeOnEscape' => false,
        'dialogClass' => 'no-close',
        'draggable' => false,
        'buttons' => array(
//            'Ok' => 'js:function(){$(this).dialog("close");}',
            'js:{text:"Ok", click:function(){$(this).dialog("close");}, disabled:true}'
        ),
    ),
));
?>
<p id="attachment-mega-login-dlg-waiting">Logging on Mega...</p>
<p id="attachment-mega-login-dlg-ok">Valid credentials.</p>
<p id="attachment-mega-login-dlg-ko">Wrong credentials.</p>
<p id="attachment-mega-login-dlg-error"></p>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<div class="row">
  <?php echo $form->labelEx($model, 'attachments[maxSize]'); ?>
  <?php echo $form->numberField($model, 'attachments[maxSize]', array('min' => 1)); ?>
  <?php echo $form->error($model, 'attachments[maxSize]'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'attachments[extList]'); ?>
  <?php echo $form->textField($model, 'attachments[extList]'); ?>
  <?php echo $form->error($model, 'attachments[extList]'); ?>
  <p class="hint">Comma separated list (eg. jpg, bmp, doc)</p>
</div>

<?php
Yii::app()->clientScript->registerScript('switch-storage', '
    var loadType = $("#ConfigForm_attachments_storage").val();
    switch (loadType) {
        case "local":
            $("#attachment-storage-mega").hide();
            break;
        case "mega":
            $("#attachment-storage-local").hide();
            break;
    }
    $("#ConfigForm_attachments_storage").on("change", function() {
        $("#attachment-storage-mega").slideToggle();
        $("#attachment-storage-local").slideToggle();
    });
', CClientScript::POS_READY);
?>

<?php
Yii::app()->clientScript->registerScript('attachment-mega-login', '
    $("#attachment-mega-login-btn").on("click", function() {
        $("#attachment-mega-login-dlg-ok, #attachment-mega-login-dlg-ko").hide();
        $("#attachment-mega-login-dlg-waiting").show();
        $("#attachment-mega-login-dlg-error").text("");
        $("#attachment-mega-login-dlg").next().find("button").button("disable");
        $("#attachment-mega-login-dlg").dialog("open");
    });
', CClientScript::POS_READY);
?>