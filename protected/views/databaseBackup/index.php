<?php
/* @var $this DatabaseBackupController */

$this->breadcrumbs = array(
    Yii::t('nav', 'Database Backups'),
);
?>
<h2><?php echo Yii::t('nav', 'Database Backups'); ?>
  <span class="actions"><?php
    echo CHtml::link(Yii::t('app', 'Backup.create.label'), array('create')),
    CHtml::image(Yii::app()->request->baseUrl . '/images/actions/database_add.png');
    ?></span>
  <div style="clear:both;height:.5em;">&nbsp;</div>
</h2>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider' => $backups,
    'columns' => array(
        'filename:text:' . Yii::t('attributes', 'Backup.file_name'),
        'filedate:datetime:' . Yii::t('attributes', 'Backup.created'),
        'filetype:text:' . Yii::t('attributes', 'Backup.file_type'),
        'filesize:size:' . Yii::t('attributes', 'Backup.file_size'),
        array(
            'class' => 'CButtonColumn',
            'template' => '{download}{delete}',
            'buttons' => array(
                'download' => array(
                    'label' => Yii::t('app', 'Backup.download.label'),
                    'options' => array('download' => ''),
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/actions/database_save.png',
                    'url' => 'Yii::app()->createUrl("databaseBackup/download", array("filename"=>$data[\'filename\']))',
                ),
                'delete' => array(
                    'label' => Yii::t('app', 'Backup.delete.label'),
                    'click' => 'js:function() {if(!confirm(' . CJavaScript::encode(Yii::t('zii', 'Are you sure you want to delete this item?')) . ')) return false;}',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/actions/database_delete.png',
                    'url' => 'Yii::app()->createUrl("databaseBackup/delete", array("filename"=>$data[\'filename\']))',
                ),
            ),
        )
    ),
));
