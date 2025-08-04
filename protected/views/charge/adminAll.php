<?php
/* @var $this ChargeController */
/* @var $model Charge */

Navigator::clearCharge();
Navigator::setChargeType('all');

$filterMonth = array();

foreach ($model->customSearch(true, true)->getData() as $singleCharge)
{
	$m = date('F Y', strtotime($singleCharge->day));
	$filterMonth[$m] = $m;
}

$this->breadcrumbs = array(
    Yii::t('nav', 'Manage All Charges'),
);

$this->menu = array(
    array('label' => 'List Charge', 'url' => array('index')),
    array('label' => 'Create Charge', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('re-load-date-picker', "
function reloadDatePicker(id, data) {
    $('#monthpicker').datepicker();
}
");
?>

<h1><?php echo Yii::t('nav', 'Manage All Charges'); ?></h1>

<?php
$this->beginWidget('CActiveForm', array(
    'id' => 'charge-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'Charge.export.{date}', array(
            '{date}' => date('Ymd')
        ));

$titleDetails = Yii::t('app', 'Charge.export.details.{date}', array(
            '{date}' => date('Ymd')
        ));

$sheet = Yii::t('app', 'Charge.sheet.{date}', array(
            '{date}' => date('Ymd')
        ));

$this->widget('ext.EChargeProjectDetailsExcelView', array(
    'title' => $title,
    'filename' => $title,
    'filenameDetails' => $titleDetails,
    'selectableRows' => 20,
    'id' => 'charge-grid',
    'dataProvider' => $model->customSearch(true),
    'dataproviderDetails' => $model->exportDetailsSearch(),
    'filter' => $model,
    'afterAjaxUpdate' => "function(id, data){
        reloadDatePicker(id, data);
        jQuery('#Charge_project_id').multiselect({
            minWidthMenu: 300,
            minWidthInput: 200,
            selectedList: 1
        });
		jQuery('#Charge_month').multiselect({
			minWidthMenu: 300,
			minWidthInput: 200,
			selectedList: 1
		});
    }",
    'template' => "{summary}\n{items}\n{exportbuttons}",
    'sheetTitle' => $sheet,
    'columns' => array(
        array(
            'name' => 'project_id',
            'filter' => CHtml::activeDropDownList($model, 'project_id', CHtml::listData(Project::model()->findAllHierarchical(array('scopes' => 'charge')), 'id', function($project) {
                      return str_pad($project->name, strlen($project->name) + 2 * $project->level, '- ', STR_PAD_LEFT);
                    }), array('multiple' => true, 'style' => 'display:none')),
            'value' => '$data->project->name'
        ),
        array(
            'name' => 'month',
            'value' => 'date(\'F Y\', strtotime($data->day))',
            'filter' => CHtml::activeDropDownList($model, 'month', $filterMonth, 
							array('multiple' => true, 'style' => 'display:none'))
        ),
        array(
            'name' => 'user_name',
            'type' => 'html',
            'value' => 'CHtml::link($data->user->username, array("charge/create", "month" => (date("d") > 15 ? date("Y-m-t") : date("Y-m-15")), "user" => $data->user_id, "manage" => 1, "half" => date("d") < 16))',
        ),
        array(
            'class' => 'CDataColumn',
            'type' => 'raw',
            'name' => 'first_total',
            'filter' => false,
            'value' => 'CHtml::link($data->firstTotal, array("charge/create", "month" => date("Y-m-15",strtotime($data->day)), "user" => $data->user_id, "project" => $data->project_id, "manage" => 1, "half" => 1))',
            'footer' => $model->getAttributeLabel('total') . ': ' . $model->getTotal($model->customSearch()->getData(), 'firstTotal'),
        ),
        array(
            'class' => 'CDataColumn',
            'type' => 'raw',
            'name' => 'second_total',
            'filter' => false,
            'value' => 'CHtml::link($data->secondTotal, array("charge/create", "month" => date("Y-m-t",strtotime($data->day)), "user" => $data->user_id, "project" => $data->project_id, "manage" => 1))',
            'footer' => $model->getAttributeLabel('total') . ': ' . $model->getTotal($model->customSearch()->getData(), 'secondTotal'),
        ),
        array(
            'name' => 'group_total',
            'value' => '$data->group_total',
            'footer' => $model->getAttributeLabel('total') . ': ' . $model->getTotal($model->customSearch()->getData(), 'group_total'),
        ),
    ),
));

$this->endWidget();

$multiselectFolder = Yii::app()->baseUrl . '/js/multiselect/';
Yii::app()->clientScript->registerScriptFile($multiselectFolder . 'jquery.multiselect.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCSSFile($multiselectFolder . 'jquery.multiselect.css');
Yii::app()->clientScript->registerScript('multiselect.filter', '
    $("#Charge_project_id").multiselect({
		minWidthMenu: 300,
		minWidthInput: 200,
		selectedList: 1
    });
    $("#Charge_month").multiselect({
        minWidthMenu: 300,
		minWidthInput: 200,
		selectedList: 1
    });
', CClientScript::POS_READY);
