<?php

Yii::import('ext.tlbExcelView');

/**
 * Description of EChargeProjectDetailsExcelView
 *
 * @author francesco.colamonici
 */
class EChargeProjectDetailsExcelView extends tlbExcelView {

  public $headerTextColor = 'FFFFFF';
  public $holidayBackgroundColor = 'E5C8C1';
  public $holidayTextColor = '070403';

  /**
   * @var CArrayDataProvider
   */
  public $dataproviderDetails = null;
  public $filenameDetails = null; //export FileName
  private $month = '2013-01';
  private $lastDay = 31;
  private $days = array();

  public function init() {
    if (isset($_GET[$this->grid_mode_var])) {
      $this->grid_mode = $_GET[$this->grid_mode_var];
    }
    if (isset($_GET['exportType'])) {
      $this->exportType = $_GET['exportType'];
    }
    $lib = Yii::getPathOfAlias($this->libPath) . '.php';
    if (($this->grid_mode == 'exportDetails') && (!file_exists($lib))) {
      $this->grid_mode = 'grid';
      Yii::log("PHP Excel lib not found($lib). Export disabled !", CLogger::LEVEL_WARNING, 'EExcelview');
    }
    if ($this->grid_mode == 'exportDetails') {
      $this->initExcel();
    } else {
      parent::init();
    }
  }

  public function initExcel() {
    parent::initExcel();
    self::$headerStyle = array(
        'borders' => array(
            'allborders' => array(
                'style' => $this->border_style,
                'color' => array('rgb' => $this->headerBorderColor),
            ),
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
            'color' => array('rgb' => $this->headerTextColor),
        )
    );
  }

  public function run() {
    if ($this->grid_mode == 'exportDetails') {
      $this->getDate();
      $this->renderHeader();
      $row = $this->renderBody();
//            $this->renderFooter($row);
      $columns = count($this->days) + 5;
      //set auto width
      if ($this->autoWidth) {
        for ($n = 0; $n < $columns; $n++) {
          $cell = self::$activeSheet->getColumnDimension($this->columnName($n + 1))->setAutoSize(true);
        }
      }

      // Set some additional properties
//            $data = $this->dataproviderDetails->getData();
//            $title = substr($this->sheetTitle . ' - ' . Project::model()->findByPk($data[0]['project_id'])->name, 0, 31);
      $title = substr(Yii::t('nav', 'Charges') . ' ' . $this->month, 0, 31);
      self::$activeSheet
              ->setTitle($title)
              ->getSheetView()->setZoomScale(100);
      self::$activeSheet->getHeaderFooter()
              ->setOddHeader('&C' . $this->sheetTitle)
              ->setOddFooter('&L&B' . self::$objPHPExcel->getProperties()->getTitle() . $this->pageFooterText);
      //2025/06/27 modified
      if (is_countable($columns))
      {
          $count_temp = count($columns);
          self::$activeSheet->getPageSetup()
                  ->setPrintArea('A1:' . $this->columnName($count_temp) . ($row + 2))
                  ->setFitToWidth();
      }

      $this->colorSheet($row, $columns);


      //create writer for saving
      $objWriter = PHPExcel_IOFactory::createWriter(self::$objPHPExcel, $this->exportType);
      if (!$this->stream) {
        $objWriter->save($this->filenameDetails);
      } else {
        //output to browser
        if (!$this->filename) {
          $this->filename = $this->title;
        }
        $this->cleanOutput();
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-type: ' . $this->mimeTypes[$this->exportType]['Content-type']);
        header('Content-Disposition: attachment; filename="' . $this->filenameDetails . '.' . $this->mimeTypes[$this->exportType]['extension'] . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        Yii::app()->end();
      }
    } else {
      parent::run();
    }
  }

  private function getDate() {
    if ($this->dataproviderDetails === null)
      throw new CHttpException(401, Yii::t('app', 'Export.charge.error'));
    $rawData = $this->dataproviderDetails->getData();
    $rndDay = array_rand($rawData[0]['charge']);
    $this->month = substr($rndDay, 0, 7);
    $this->lastDay = CPropertyValue::ensureInteger(date('t', strtotime($rndDay)));
  }

  private function isHoliday($day) {
    $dow = date('w', strtotime($day));
    return $dow == 0 || $dow == 6;
  }

  public function renderHeader() {
    if ($this->grid_mode == 'exportDetails') {
      $a = 1;

      $head = Yii::t('attributes', 'Charge.project_id');
      $cell = self::$activeSheet->setCellValue($this->columnName($a++) . '1', $head, true);
      if (is_callable($this->onRenderHeaderCell)) {
        call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
      }

      $head = Yii::t('attributes', 'Charge.user_id');
      $cell = self::$activeSheet->setCellValue($this->columnName($a++) . '1', $head, true);
      if (is_callable($this->onRenderHeaderCell)) {
        call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
      }
	  
      $head = Yii::t('attributes', 'Charge.task_id');
      $cell = self::$activeSheet->setCellValue($this->columnName($a++) . '1', $head, true);
      if (is_callable($this->onRenderHeaderCell)) {
        call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
      }

      for ($d = 1; $d <= $this->lastDay; $d++) {
        $day = $this->month . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
        array_push($this->days, $day);
        $head = date('d/m', strtotime($day));
        $cell = self::$activeSheet->setCellValue($this->columnName($a++) . '1', $head, true);
        if (is_callable($this->onRenderHeaderCell)) {
          call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
        }
      }

      $head = Yii::t('attributes', 'Charge.first_total');
      $cell = self::$activeSheet->setCellValue($this->columnName($a++) . '1', $head, true);
      if (is_callable($this->onRenderHeaderCell)) {
        call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
      }

      $head = Yii::t('attributes', 'Charge.second_total');
      $cell = self::$activeSheet->setCellValue($this->columnName($a++) . '1', $head, true);
      if (is_callable($this->onRenderHeaderCell)) {
        call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
      }

      $head = Yii::t('attributes', 'Charge.total');
      $cell = self::$activeSheet->setCellValue($this->columnName($a) . '1', $head, true);
      if (is_callable($this->onRenderHeaderCell)) {
        call_user_func_array($this->onRenderHeaderCell, array($cell, $head));
      }

      // Format the header row
      $header = self::$activeSheet->getStyle($this->columnName(1) . '1:' . $this->columnName($a) . '1');
      $header->getAlignment()
              ->setHorizontal(self::$horizontal_center)
              ->setVertical(self::$vertical_center);
      $header->applyFromArray(self::$headerStyle);
      self::$activeSheet->getRowDimension(1)->setRowHeight($this->headerHeight);
    } else {
      return parent::renderHeader();
    }
  }

  public function renderBody() {
    if ($this->grid_mode == 'exportDetails') {
      $data = $this->dataproviderDetails->getData();
      $n = count($data);

      if ($n > 0) {
        $row = 0;
        foreach ($data as $charge) {
          $this->renderRow($row++, $charge);
        }
        return $n;
      }
    } else {
      return parent::renderBody();
    }
  }

  public function renderRow($row, $params = array()) {
    if ($this->grid_mode == 'exportDetails') {
      extract($params);
      $data = $this->dataproviderDetails->getData();
//        $total = 0.0;

      $a = 1;

      $value = $project;
      $content = strip_tags($value);
      $cell = self::$activeSheet->setCellValue($this->columnName($a++) . ($row + 2), $content, true);

      if (is_callable($this->onRenderDataCell)) {
        call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
      }
	  
      $value = $user;
      $content = strip_tags($value);
      $cell = self::$activeSheet->setCellValue($this->columnName($a++) . ($row + 2), $content, true);

      if (is_callable($this->onRenderDataCell)) {
        call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
      }

      $value = $task;
      $content = strip_tags($value);
      $cell = self::$activeSheet->setCellValue($this->columnName($a) . ($row + 2), $content, true);

      if (is_callable($this->onRenderDataCell)) {
        call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
      }

      $sumTotalStart = $this->columnName($a + 1) . ($row + 2);
      $sumFirstTotalStart = $this->columnName($a + 1) . ($row + 2);
      $sumSecondTotalStart = '';
      foreach ($this->days as $day) {
        $value = isset($charge[$day]) ? CPropertyValue::ensureFloat($charge[$day]) : 0.0;
//            $total += $value;
        $a++;

        if (CPropertyValue::ensureInteger(substr($day, 8)) === 15) {
          $sumFirstTotalEnd = $this->columnName($a) . ($row + 2);
          $sumSecondTotalStart = $this->columnName($a + 1) . ($row + 2);
        }

        // Check if the cell value is a number, then format it accordingly
        // May be improved notably by exposing the formats as public
        // May be usable only for French-style number formatting ?
        /*
          if (preg_match("/^[0-9]*\\" . $this->thousandsSeparator . "[0-9]*\\" . $this->decimalSeparator . "[0-9]*$/",
          strip_tags($value))) {
          $content = str_replace($this->decimalSeparator, '.',
          str_replace($this->thousandsSeparator, '', strip_tags($value)));
          $format = '#\.##0.00';
          } else if (preg_match("/^[0-9]*\\" . $this->decimalSeparator . "[0-9]*$/", strip_tags($value))) {
          $content = str_replace($this->decimalSeparator, '.', strip_tags($value));
          $format = '0.00';
          } else if (!$this->displayZeros && ((strip_tags($value) === '0') || (strip_tags($value) === $this->zeroPlaceholder))) {
          $content = $this->zeroPlaceholder;
          self::$activeSheet->getStyle($this->columnName($a) . ($row + 2))->getAlignment()->setHorizontal(self::$horizontal_right);
          $format = '0.00';
          } else {
          $content = strip_tags($value);
          $format = null;
          }
         */
        $content = strip_tags($value);
        $format = null;

        $cell = self::$activeSheet->setCellValue($this->columnName($a) . ($row + 2), $content, true);

        // Format each cell's number - if any
        if (!is_null($format)) {
          self::$summableColumns[$a] = $a;
          self::$activeSheet->getStyle($this->columnName($a) . ($row + 2))->getNumberFormat()->setFormatCode($format);
        }

        if (is_callable($this->onRenderDataCell)) {
          call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
        }
      }

//        $content = strip_tags($total);

      $sumTotalEnd = $this->columnName($a) . ($row + 2);
      $sumSecondTotalEnd = $this->columnName($a) . ($row + 2);

      $content = "=SUM($sumFirstTotalStart:$sumFirstTotalEnd)";
      $cell = self::$activeSheet->setCellValue($this->columnName(++$a) . ($row + 2), $content, true);
      if (is_callable($this->onRenderDataCell)) {
        call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
      }

      $content = "=SUM($sumSecondTotalStart:$sumSecondTotalEnd)";
      $cell = self::$activeSheet->setCellValue($this->columnName(++$a) . ($row + 2), $content, true);
      if (is_callable($this->onRenderDataCell)) {
        call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
      }

      $content = "=SUM($sumTotalStart:$sumTotalEnd)";
      $cell = self::$activeSheet->setCellValue($this->columnName(++$a) . ($row + 2), $content, true);
      if (is_callable($this->onRenderDataCell)) {
        call_user_func_array($this->onRenderDataCell, array($cell, $data[$row], $value));
      }

      // Format the row globally
      $renderedRow = self::$activeSheet->getStyle('A' . ($row + 2) . ':' . $this->columnName($a) . ($row + 2));
      $renderedRow->getAlignment()->setVertical(self::$vertical_center);
      $renderedRow->applyFromArray(self::$style);
      self::$activeSheet->getRowDimension($row + 2)->setRowHeight($this->rowHeight);
    } else {
      return parent::renderRow($row, $params);
    }
  }

  public function renderExportButtons() {
    foreach ($this->exportButtons as $key => $button) {
      $item = is_array($button) ? CMap::mergeArray($this->mimeTypes[$key], $button) : $this->mimeTypes[$button];
      $type = is_array($button) ? $key : $button;
      $submitParams = array(Yii::app()->controller->action->id, 'exportType' => $type, 'grid_mode' => 'export');
      foreach ($_GET as $param => $value)
        $submitParams[$param] = $value;
      $content[] = CHtml::link($item['caption'] . ' ' . CHtml::image(Yii::app()->baseUrl . '/' . $item['icon'], '', array('width' => 16, 'height' => 16)), '#', array('submit' => $submitParams));
    }
    $submitParams = array(Yii::app()->controller->action->id, 'exportType' => $type, 'grid_mode' => 'exportDetails');
    $content[] = CHtml::link($item['caption'] . ' - ' . Yii::t('app', 'Export.charge.details') . ' ' . CHtml::image(Yii::app()->baseUrl . '/' . $item['icon'], '', array('width' => 16, 'height' => 16)), '#', array('id' => 'exportDetailsLink', 'submit' => $submitParams));
    if ($content) {
      echo '<div class="excel-footer">';
      $this->renderPager();
      echo '<div class="', $this->exportButtonsCSS, '">';
      echo Yii::t('nav', $this->exportText) . ':&nbsp;&nbsp;&nbsp;&nbsp;' . implode('&nbsp;', $content);
      echo '</div>';
      echo '<div style="clear:both;"></div>';
      echo '</div>';
    }

    $alert = Yii::t('app', 'Export.charge.alert');
    Yii::app()->getClientScript()->registerScript('exportDetailsLink', '
            jQuery("body").on("click","#exportDetailsLink",function(e) {
                var month = $("[name*=\'month\']").val();
                var project = $("[name*=\'project_id\']").val();
                if (!project || !month || month.length != 1) {
                    alert("' . $alert . '");
                    $(e).stopImmediatePropagation();
                }
            });
        ', CClientScript::POS_END);
  }

  private function colorSheet($rows, $columns) {
    $rows++;
    $lastColumnName = $this->columnName($columns);
    self::$activeSheet->getStyle('A1:' . $lastColumnName . $rows)
            ->applyFromArray(array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
    ));
    self::$activeSheet->getStyle('A1:' . $lastColumnName . '1')
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
    for ($i = 2; $i <= $rows; $i++) {
      self::$activeSheet->getStyle('A' . $i . ':' . $lastColumnName . $i)
              ->applyFromArray(array(
                  'fill' => array(
                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => array(
                          'rgb' => $i % 2 === 0 ? 'E5F1F4' : 'F8F8F8'
                      ),
                  ),
      ));
      for ($j = 4; $j < $columns - 3; $j++) {
        if ($this->isHoliday($this->days[$j - 4])) {
          self::$activeSheet->getStyle($this->columnName($j) . $i)
                  ->applyFromArray(array(
                      'fill' => array(
                          'type' => PHPExcel_Style_Fill::FILL_SOLID,
                          'color' => array('rgb' => $this->holidayBackgroundColor),
                      ),
                      'font' => array(
                          'color' => array('rgb' => $this->holidayTextColor)
                      )
          ));
        }
      }
      for ($j = $columns - 1; $j <= $columns + 1; $j++) {
        self::$activeSheet->getStyle($this->columnName($j) . $i)
                ->applyFromArray(array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => $i % 2 === 0 ? 'A0CCD7' : 'D7EAEE'
                        ),
                    ),
                    'font' => array(
                        'bold' => true,
                    )
        ));
      }
    }
  }

}
