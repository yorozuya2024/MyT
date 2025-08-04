<?php
/* @var $this AttachmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    Yii::t('nav', 'Attachments'),
);

$this->menu = array(
    array('label' => 'Create Attachment', 'url' => array('create')),
    array('label' => 'Manage Attachment', 'url' => array('admin')),
);
?>

<h2><?php echo Yii::t('nav', 'Attachments'); ?></h2>

<?php
$this->widget('zii.widgets.CListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => '_view',
));
?>

<h3>Test</h3>

<?php
echo CHtml::link('Mega Test Download', array('download', 'id' => 1));
?>
