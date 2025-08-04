<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div id="content">
  <?php if (isset($this->breadcrumbs)): ?>
    <?php
    $this->widget('zii.widgets.CBreadcrumbs', array(
        'links' => $this->breadcrumbs,
    ));
    ?><!-- breadcrumbs -->
  <?php endif ?>

  <hr />
  <?php $this->widget('application.extensions.FlashMessage'); ?>
  <?php echo $content; ?>
</div><!-- content -->
<?php $this->endContent(); ?>