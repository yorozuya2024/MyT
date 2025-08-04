<?php
/* @var $this DatabaseBackupController */

$this->breadcrumbs = array(
    Yii::t('nav', 'Check for update'),
);
?>
<h2><?php echo Yii::t('nav', 'Check for update'); ?>
  <div style="clear:both;height:.5em;">&nbsp;</div>
</h2>
<?php if( !empty($version) ):
	$this->widget('zii.widgets.CDetailView', array(
        'data'=> $version,
        'attributes'=>array(
                'your_version:text:' . Yii::t('attributes', 'CheckUpdate.your_version'),
                'last_version:text:' . Yii::t('attributes', 'CheckUpdate.last_version'),
                'download_url:url:'. Yii::t('attributes', 'CheckUpdate.download_url'),
                'release_date:datetime:'. Yii::t('attributes', 'CheckUpdate.release_date'),
                'type:text:'. Yii::t('attributes', 'CheckUpdate.type'),
			)
		));
		else: ?>
		<h3><?php echo Yii::t('app', 'CheckUpdate.view.nodata'); ?></h3>
<?php endif; ?>
