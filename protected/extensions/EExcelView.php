<?php

Yii::import('zii.widgets.grid.CGridView');

/**
 * @author Nikola Kostadinov
 * @license MIT License
 * @version 0.3
 */
class EExcelView extends CGridView {

    //Document properties
    public $creator = 'Nikola Kostadinov';
    public $title = null;
    public $subject = 'Subject';
    public $description = '';
    public $category = '';
    private $_lastRow = 0;
    //the PHPExcel object
    public $objPHPExcel = null;
    public $libPath = 'ext.PHPExcel.Classes.PHPExcel'; //the path to the PHP excel lib
    //config
    public $autoWidth = true;
    public $exportType = 'Excel5';
    public $disablePaging = true;
    public $filename = null; //export FileName
    public $stream = true; //stream to browser
    public $grid_mode = 'grid'; //Whether to display grid ot export it to selected format. Possible values(grid, export)
    public $grid_mode_var = 'grid_mode'; //GET var for the grid mode
    //buttons config
    public $exportButtonsCSS = 'export-buttons';
    public $exportButtons = array('Excel2007', 'CSV', 'HTML');
    public $exportText = 'Export to';
    //callbacks
    public $onRenderHeaderCell = null;
    public $onRenderDataCell = null;
    public $onRenderFooterCell = null;
    //mime types used for streaming
    public $mimeTypes = array(
        'Excel5' => array(
            'Content-type' => 'application/vnd.ms-excel',
            'extension' => 'xls',
            'caption' => 'Excel',
            'icon' => 'images/actions/excel.png',
        ),
        'Excel2007' => array(
            'Content-type' => 'application/vnd.ms-excel',
            'extension' => 'xlsx',
            'caption' => 'Excel',
            'icon' => 'images/actions/excel.png',
        ),
//        'PDF' => array(
//            'Content-type' => 'application/pdf',
//            'extension' => 'pdf',
//            'caption' => 'PDF(*.pdf)',
//            'icon' => 'images/actions/pdf.png',
//        ),
        'HTML' => array(
            'Content-type' => 'text/html',
            'extension' => 'html',
            'caption' => 'HTML',
            'icon' => 'images/actions/html.png',
        ),
        'CSV' => array(
            'Content-type' => 'application/csv',
            'extension' => 'csv',
            'caption' => 'CSV',
            'icon' => 'images/actions/csv.png',
        )
    );

    public function init() {
        if (isset($_GET[$this->grid_mode_var]))
            $this->grid_mode = $_GET[$this->grid_mode_var];
        if (isset($_GET['exportType']))
            $this->exportType = $_GET['exportType'];
        $this->template = "{summary}\n{items}\n{exportbuttons}"; //\n{pager}";
        $this->creator = Yii::app()->params['name'];
        array_unshift($this->columns, array(
            'id' => 'chkId',
            'name' => 'chkId',
            'value' => '$data->id',
            'class' => 'CCheckBoxColumn'
        ));
        Yii::app()->clientScript->registerScript('no-row-selection', '
            $(document).on("click.yiiGridView", ".grid-view td:not(\'.checkbox-column\')", function(){$(this).closest("tr").toggleClass("selected");});
            $(document).on("click.yiiGridView", ".grid-view .button-column", function(){$(this).closest("tr").toggleClass("selected");});
            $(document).on("click.yiiGridView", ".grid-view .filters", function(){$(this).closest("tr").toggleClass("selected");});
        ');

        $lib = Yii::getPathOfAlias($this->libPath) . '.php';
        if ($this->grid_mode == 'export' and !file_exists($lib)) {
            $this->grid_mode = 'grid';
            Yii::log("PHP Excel lib not found($lib). Export disabled !", CLogger::LEVEL_WARNING, 'EExcelview');
        }

        if ($this->grid_mode == 'export') {
            $this->title = $this->title ? $this->title : Yii::app()->getController()->getPageTitle();
            $this->initColumns();
            //parent::init();
            //Autoload fix
            spl_autoload_unregister(array('YiiBase', 'autoload'));
            Yii::import($this->libPath, true);
            $this->objPHPExcel = new PHPExcel();
            PHPExcel_Shared_File::setUseUploadTempDirectory(true);
            spl_autoload_register(array('YiiBase', 'autoload'));
            // Creating a workbook
            $this->objPHPExcel->getProperties()->setCreator($this->creator);
            $this->objPHPExcel->getProperties()->setTitle($this->title);
            $this->objPHPExcel->getProperties()->setSubject($this->subject);
            $this->objPHPExcel->getProperties()->setDescription($this->description);
            $this->objPHPExcel->getProperties()->setCategory($this->category);
        }
        else
            parent::init();
    }

    public function renderHeader() {
        $a = 0;
        foreach ($this->columns as $column) {
            if ($column instanceof CButtonColumn)
//                $head = $column->header;
                continue;
            elseif ($column instanceof CCheckBoxColumn)
                continue;
            elseif ($column->header === null && $column->name !== null) {
                if ($column->grid->dataProvider instanceof CActiveDataProvider)
                    $head = $column->grid->dataProvider->model->getAttributeLabel($column->name);
                else
                    $head = $column->name;
            }
            else
                $head = trim($column->header) !== '' ? $column->header : $column->grid->blankDisplay;

            $a++;
            $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName($a) . "1", $head, true);
            if (is_callable($this->onRenderHeaderCell))
                call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
        }
    }

    public function renderBody() {
        if ($this->disablePaging) //if needed disable paging to export all data
            $this->dataProvider->pagination = false;

        $data = $this->dataProvider->getData();
        $n = count($data);

        $chkIdAll = isset($_POST['chkId']) ? $_POST['chkId'] : array();

        if (count($chkIdAll) > 0 && $n > 0) {
            for ($row = 0; $row < $n; ++$row) {
                if (in_array(CHtml::value($data[$row], 'id'), $chkIdAll))
                    $this->renderRow($row);
            }
        }
        return $n;
    }

    public function renderRow($row) {
        $data = $this->dataProvider->getData();

        $a = 0;
        foreach ($this->columns as $n => $column) {
            if ($column instanceof CLinkColumn) {
                if ($column->labelExpression !== null)
                    $value = $column->evaluateExpression($column->labelExpression, array('data' => $data[$row], 'row' => $row));
                else
                    $value = $column->label;
            } elseif ($column instanceof CButtonColumn)
//                $value = ""; //Dont know what to do with buttons
                continue;
            elseif ($column instanceof CCheckBoxColumn)
                continue;
            elseif ($column->value !== null)
                $value = $this->evaluateExpression($column->value, array('data' => $data[$row]));
            elseif ($column->name !== null) {
                //$value=$data[$row][$column->name];
                $value = CHtml::value($data[$row], $column->name);
                $value = $value === null ? '' : $column->grid->getFormatter()->format($value, 'raw');
            }

            $a++;
            //2025/08/04 modified
            $cleanValue = strip_tags($value ?? '');
            //$cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName($a) . ($this->_lastRow + 2), //strip_tags($value), true);
            $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName($a) . ($this->_lastRow + 2), $cleanValue, true);
            if (is_callable($this->onRenderDataCell))
                call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
        }
        $this->_lastRow++;
    }

    public function renderFooter($row) {
        $a = 0;
        foreach ($this->columns as $n => $column) {
            $a = $a + 1;
            if ($column->footer) {
                $footer = trim($column->footer) !== '' ? $column->footer : $column->grid->blankDisplay;

                $cell = $this->objPHPExcel->getActiveSheet()->setCellValue($this->columnName($a) . ($row + 2), $footer, true);
                if (is_callable($this->onRenderFooterCell))
                    call_user_func_array($this->onRenderFooterCell, array($cell, $footer));
            }
        }
    }

    public function run() {
        if ($this->grid_mode == 'export') {
            $this->renderHeader();
            $row = $this->renderBody();
            $this->renderFooter($this->_lastRow);

            //set auto width
            if ($this->autoWidth)
                foreach ($this->columns as $n => $column)
                    $this->objPHPExcel->getActiveSheet()->getColumnDimension($this->columnName($n + 1))->setAutoSize(true);

            $lastColumnIndex = 0;
            foreach ($this->columns as $column)
                if (!($column instanceof CButtonColumn || $column instanceof CCheckBoxColumn))
                    $lastColumnIndex++;

            $lastColumnName = $this->columnName($lastColumnIndex);
            $this->objPHPExcel->getActiveSheet()->getStyle('A1:' . $lastColumnName . ++$this->_lastRow)
                    ->applyFromArray(array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
            ));
            $this->objPHPExcel->getActiveSheet()->getStyle('A1:' . $lastColumnName . '1')
                    ->applyFromArray(array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation' => 0,
                            'wrap' => TRUE
                        ),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 270,
                            'startcolor' => array(
                                'rgb' => '6FACCF'
                            ),
                            'endcolor' => array(
                                'rgb' => 'A8CDE2'
                            )
                        ),
                        'font' => array(
                            'bold' => true,
                            'color' => array(
                                'rgb' => 'FFFFFF'
                            )
                        )
            ));
            for ($i = 2; $i <= $this->_lastRow; $i++)
                $this->objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':' . $lastColumnName . $i)
                        ->applyFromArray(array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array(
                                    'rgb' => $i % 2 === 0 ? 'E5F1F4' : 'F8F8F8'
                                ),
                            ),
                ));

            //create writer for saving
            $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->exportType);
            if (!$this->stream)
                $objWriter->save($this->filename);
            else { //output to browser
                if (!$this->filename)
                    $this->filename = $this->title;
                $this->cleanOutput();
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-type: ' . $this->mimeTypes[$this->exportType]['Content-type']);
                header('Content-Disposition: attachment; filename="' . $this->filename . '.' . $this->mimeTypes[$this->exportType]['extension'] . '"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                Yii::app()->end();
            }
        }
        else
            parent::run();
    }

    /**
     * Returns the coresponding excel column.(Abdul Rehman from yii forum)
     *
     * @param int $index
     * @return string
     */
    public function columnName($index) {
        --$index;
        if ($index >= 0 && $index < 26)
            return chr(ord('A') + $index);
        else if ($index > 25)
            return ($this->columnName($index / 26)) . ($this->columnName($index % 26 + 1));
        else
            throw new Exception("Invalid Column # " . ($index + 1));
    }

    public function renderExportButtons() {

        foreach ($this->exportButtons as $key => $button) {
            $item = is_array($button) ? CMap::mergeArray($this->mimeTypes[$key], $button) : $this->mimeTypes[$button];
            $type = is_array($button) ? $key : $button;
//            $url = parse_url(Yii::app()->request->requestUri);
            //$content[] = CHtml::link($item['caption'], '?'.$url['query'].'exportType='.$type.'&'.$this->grid_mode_var.'=export');
//            $content = array();
//            if (key_exists('query', $url))
//                $content[] = CHtml::link($item['caption'], '?' . $url['query'] . '&exportType=' . $type . '&' . $this->grid_mode_var . '=export');
//            else
//                $content[] = CHtml::link($item['caption'], '?exportType=' . $type . '&' . $this->grid_mode_var . '=export');
            $submitParams = array(Yii::app()->controller->action->id, 'exportType' => $type, 'grid_mode' => 'export');
            foreach ($_GET as $param => $value)
                $submitParams[$param] = $value;
            $content[] = CHtml::link($item['caption'] . ' ' . CHtml::image(Yii::app()->baseUrl . '/' . $item['icon'], '', array('width' => 16, 'height' => 16)), '#', array('submit' => $submitParams));
        }
        if ($content) {
//            echo CHtml::tag('div', array('class' => $this->exportButtonsCSS), $this->exportText . implode('&nbsp;', $content) . $this->renderPager());
            echo '<div class="excel-footer">';
            $this->renderPager();
            echo '<div class="', $this->exportButtonsCSS, '">';
            echo Yii::t('nav', $this->exportText) . ':&nbsp;&nbsp;&nbsp;&nbsp;' . implode('&nbsp;', $content);
            echo '</div>';
            echo '<div style="clear:both;"></div>';
            echo '</div>';
        }
    }

    /**
     * Performs cleaning on mutliple levels.
     *
     * From le_top @ yiiframework.com
     *
     */
    protected static function cleanOutput() {
        for ($level = ob_get_level(); $level > 0; --$level) {
            @ob_end_clean();
        }
    }

}