<?php
/* @var $this ChargeController */
/* @var $model Charge */
/* @var $records Charge[] */
/* @var $projects Project[] */
/* @var $date string */
/* @var $userId integer */
/* @var $prjId integer */

//2025/06/30 modified
//$numRows = 10; // Should be moved to conifg
//$numRows = 20; // Should be moved to conifg
//2025/07/31 modified
$numRows = 30; // Should be moved to conifg

$arr = explode('/', $date);

//$days = ChargeHelper::generateMonth( $arr[1], $arr[2] );
$days = ChargeHelper::generateQuindicina($arr[0], $arr[1], $arr[2]);

$currentDate = ChargeHelper::getQuindicina($arr[0], $arr[1], $arr[2]);
$prevArr = ChargeHelper::getListPrevQuindicine(5);

$totalByDay = array();
$totalByRow = array();
?>

<div class="toggle"><?php
  echo CHtml::button('<', array(
      'ajax' => array(
          'type' => 'POST',
          'url' => CController::createUrl('charge/getGrid'),
          'data' => array('date' => 'js: $("#half_select").val()', 'mode' => 'prev', 'user' => $userId, 'project' => $prjId),
          'success' => 'js:function(html){
                jQuery("#charge-table").html(html);
                jQuery("#half_select").datepicker({"dateFormat":"dd/mm/yy"});
            }'
      )
          )
  );
  echo CHtml::encode(' ');
  $this->widget('zii.widgets.jui.CJuiDatePicker', array(
      'name' => 'half_select',
      'value' => $currentDate,
      'options' => array(
          'dateFormat' => 'dd/mm/yy'
      ),
      'htmlOptions' => array(
          'size' => '10',
          'maxlength' => '10',
          'class' => 'monthpicker',
          'ajax' => array(
              'type' => 'POST',
              'url' => CController::createUrl('charge/getGrid'),
              'data' => array('date' => 'js: $(this).val()', 'user' => $userId, 'project' => $prjId),
              'success' => 'js:function(html){
                    jQuery("#charge-table").html(html);
                    jQuery("#half_select").datepicker({"dateFormat":"dd/mm/yy"});
                }'
          )
      ),
  ));
  echo CHtml::encode(' ');
  echo CHtml::button('>', array(
      'ajax' => array(
          'type' => 'POST',
          'url' => CController::createUrl('charge/getGrid'),
          'data' => array('date' => 'js: $("#half_select").val()', 'mode' => 'next', 'user' => $userId, 'project' => $prjId),
          'success' => 'js:function(html){
                jQuery("#charge-table").html(html);
                jQuery("#half_select").datepicker({"dateFormat":"dd/mm/yy"});
            }',
          'error' => 'js:function(data){console.log(data.responseText);}'
      )
          )
  );
  ?>
</div>

<div class="grid-view">
  <table id="timesheet">
    <thead>
      <tr>
        <th><?php echo CHtml::activeLabelEx($model, 'project_id'); ?></th>
        <th><?php echo CHtml::activeLabelEx($model, 'task_id'); ?></th>
        <?php foreach ($days as $day => $dow): ?>
          <th class="<?php echo ChargeHelper::getCSSClass($day, $dow); ?>"><?php echo $dow; ?><br /><?php echo $day; ?></th>
        <?php endforeach; ?>
        <th><?php echo $model->getAttributeLabel('total'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i = 0; $i <= $numRows; $i++): ?>
        <?php
        $record = isset($records[$i]) ? $records[$i] : $model;
        $charges = isset($record['charges']) ? $record['charges'] : array();

        echo CHtml::hiddenField("Charge[month][$i]", $currentDate);
        echo CHtml::hiddenField("Charge[user_id][$i]", $userId);
        ?>
        <tr class="row<?php echo $i % 2 == 0 ? " even" : ""; ?>">
          <td class="wbs-cell" id="<?php echo $i; ?>-wbs">
            <?php
            echo CHtml::dropDownList("Charge[project_id][$i]", $record['project_id'], CHtml::listData($projects, 'id', function($project) {
                      return str_pad($project->name, strlen($project->name) + 2 * $project->level, '- ', STR_PAD_LEFT);
                    }), array('id' => 'row-' . $i . '-wbs',
                'class' => 'wbs-select',
                'empty' => '',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => CController::createUrl('charge/getTasks'),
                    'data' => array('project_id' => 'js: $(this).val()',
                        'selected' => 'js: $("#row-' . $i . '-task").val()'),
                    'update' => '#row-' . $i . '-task',
                )
                    )
            );
            ?>
            <span id="row-<?php echo $i; ?>-placeholder"></span>
          </td>
          <td class="task-cell" id="<?php echo $i; ?>-task">
            <?php
            echo CHtml::dropDownList("Charge[task_id][$i]", $record['task_id'], CHtml::listData($record['alltask'], 'id', function($task) {
                      return str_pad($task->title, strlen($task->title) + 2 * $task->level, '- ', STR_PAD_LEFT);
                    }), array('id' => 'row-' . $i . '-task',
                'class' => 'task-select',
                'empty' => ''
                    )
            );
            ?>
            <span id="row-<?php echo $i; ?>-taskplaceholder"></span>
          </td>
          <?php foreach ($days as $day => $dow): ?>
            <td class="charge_cell <?php echo ChargeHelper::getCSSClass($day, $dow); ?>">
              <?php
              $charge = isset($charges[$day]) ? $charges[$day] : array('id' => 0, 'hours' => '');
              echo CHtml::hiddenField("Charge[charge_data_id][$i][$day]", $charge['id']);
              echo CHtml::textField("Charge[charge_data][$i][$day]", $charge['hours'], array('id' => 'row-' . $i . '-charge-' . $day,
                  'class' => 'charge',
                  'disabled' => 'disabled',
                  'maxlength' => 4,
                  'value' => $charge['hours'],
                      )
              );

              if (!empty($record['project_id'])) {
                $totalByDay[$day] = isset($totalByDay[$day]) ? (float)$charge['hours'] + $totalByDay[$day] : (float)$charge['hours'];
                // 2024/9/11 modified
                //$totalByRow[$i] = isset($totalByRow[$i]) ? $charge['hours'] + $totalByRow[$i] : $charge['hours'];
                $totalByRow[$i] = isset($totalByRow[$i]) ? (float)$charge['hours'] + (float)$totalByRow[$i] : (float)$charge['hours'];
              }
              ?>
            </td>
          <?php endforeach; ?>
          <td class="total_<?php echo $i; ?>"><?php echo isset($totalByRow[$i]) ? $totalByRow[$i] : ''; ?></td>
        </tr>
      <?php endfor; ?>
    </tbody>
    <tfoot>
      <tr>
        <td><?php echo $model->getAttributeLabel('day_total'); ?></td>
        <td>&nbsp;</td>
        <?php foreach ($days as $day => $dow): ?>
          <td class="total_per_day <?php echo ChargeHelper::getCSSClass($day, $dow); ?>" id="day_total_<?php echo $day; ?>"><?php
            echo isset($totalByDay[$day]) ? $totalByDay[$day] : ''
            ?></td>
        <?php endforeach; ?>
        <td id="total_overall"><?php echo array_sum($totalByRow); ?></td>
      </tr>
    </tfoot>
  </table>
</div>

<?php if (isset($isAjax) && $isAjax): ?>
  <script type="text/javascript">MyTGridLoaded(<?php echo $arr[0]; ?>);</script>
<?php endif; ?>