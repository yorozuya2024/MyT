<?php

Yii::import('ext.EExcelView');

/**
 * @author Nikola Kostadinov
 * @license MIT License
 * @version 0.3
 */
class TaskExcelView extends EExcelView {

    public function renderExportButtons() {
        foreach ($this->exportButtons as $key => $button) {
            $item = is_array($button) ? CMap::mergeArray($this->mimeTypes[$key], $button) : $this->mimeTypes[$button];
            $type = is_array($button) ? $key : $button;
            $submitParams = array(Yii::app()->controller->action->id, 'exportType' => $type, 'grid_mode' => 'export');
            foreach ($_GET as $param => $value)
                $submitParams[$param] = $value;
            $content[] = CHtml::link($item['caption'] . ' ' . CHtml::image(Yii::app()->baseUrl . '/' . $item['icon'], '', array('width' => 16, 'height' => 16)), '#', array('submit' => $submitParams));
        }
        if ($content) {
            echo '<div class="excel-footer">';
            $this->renderPager();
            echo '<div class="', $this->exportButtonsCSS, '">';
            if (Yii::app()->user->checkAccess('updateTask')) {
                echo CHtml::link(Yii::t('nav', 'Edit Selected') . ' ' . CHtml::image(Yii::app()->baseUrl . '/images/actions/database_edit.png', '', array('width' => 16, 'height' => 16)), '#', array(
                    'onclick' => '$("#task-massive-update").dialog("open");
                    $("#task-massive-form")[0].reset();
                    var ids = [];
//                    $("input:checkbox:checked").each(function() {
                    $("[name=\'chkId[]\']:checked").each(function() {
                        ids.push($(this).val());
                    });
                    $("#TaskMassiveForm_ids").val(ids.join(","));
                    $("#TaskMassiveForm_owner").val("");
                    $("#TaskMassiveFormFake_end_date_1").datepicker("option", "minDate", null);
                    return false;',
                ));
                echo '<span class="separator"></span>';
            }
            echo Yii::t('nav', $this->exportText) . ':&nbsp;&nbsp;&nbsp;&nbsp;' . implode('&nbsp;', $content);
            echo '</div>';
            echo '<div style="clear:both;"></div>';
            echo '</div>';
        }
    }

}
