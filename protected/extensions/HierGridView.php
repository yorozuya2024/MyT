<?php

Yii::import('zii.widgets.grid.CGridView');

/**
 * Description of HierGridView
 *
 * @author francesco.colamonici
 */
class HierGridView extends CGridView {

  /**
   * @var string the base script URL for all treeTable view resources (e.g. javascript, CSS file, images).
   * Defaults to null, meaning using the integrated grid view resources (which are published as assets).
   */
  public $baseTreeTableUrl;
  
  public $treeColumn = 0;

  /**
   * Initializes the tree grid view.
   */
  public function init() {
    parent::init();
    if ($this->baseTreeTableUrl === null)
      $this->baseTreeTableUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.QTreeGridView.treeTable'));


    //Calc parent id from nesteD set
    if (count($this->dataProvider->data)) {
      $left = $this->dataProvider->data[0]->id;
      $right = $this->dataProvider->data[0]->parent_id;
      $level = 1; // "parent_id";
      $stack = array();
      $currentLevel = 0;
      $previousModel = null;
      try {
        foreach ($this->dataProvider->data as $model) {
          if ($model->$level == 1) { //root with level=1
            $model->parentId = 0;
            $currentLevel = 1;
          } else {
            if ($model->$level == $currentLevel) {
              if (is_null($stack[count($stack) - 1])) {
                throw new Exception('Tree is corrupted');
              }
              $model->parentId = $stack[count($stack) - 1]->getPrimaryKey();
            } elseif ($model->$level > $currentLevel) {
              if (is_null($previousModel)) {
                throw new Exception('Tree is corrupted');
              }
              $currentLevel = $model->$level;
              $model->parentId = $previousModel->getPrimaryKey();
              array_push($stack, $previousModel);
            } elseif ($model->$level < $currentLevel) {
              for ($i = 0; $i < $currentLevel - $model->$level; $i++) {
                array_pop($stack);
              }
              if (is_null($stack[count($stack) - 1])) {
                throw new Exception('Tree is corrupted');
              }
              $currentLevel = $model->$level;
              $model->parentId = $stack[count($stack) - 1]->getPrimaryKey();
            }
          }
          $previousModel = $model;
        }
      } catch (Exception $e) {
        // Yii::app()->user->setFlash('CQTeeGridView', $e->getMessage());
      }
    }
  }

  /**
   * Registers necessary client scripts.
   */
  public function registerClientScript() {
    parent::registerClientScript();

    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile($this->baseTreeTableUrl . '/javascripts/jquery.treeTable.js', CClientScript::POS_END);
    $cs->registerCssFile($this->baseTreeTableUrl . '/stylesheets/jquery.treeTable.css');

    $cs->registerScript('treeTable_' . $this->getId(), '
            $(document).ready(function()  {
              $("#' . $this->getId() . ' .items").treeTable({treeColumn:'. $this->treeColumn . '});
            });
            ');
  }

  /**
   * Renders the data items for the grid view.
   */
  public function renderItems() {

    if (Yii::app()->user->hasFlash('CQTeeGridView')) {
      print '<div style="background-color:#ffeeee;padding:7px;border:2px solid #cc0000;">' . Yii::app()->user->getFlash("CQTeeGridView") . '</div>';
    }
    parent::renderItems();
  }

  /**
   * Renders a table body row with id and parentId, needed for ActsAsTreeTable
   * jQuery extension.
   * @param integer $row the row number (zero-based).
   */
  public function renderTableRow($row) {
    $model = $this->dataProvider->data[$row];
    $parentClass = $model->parent_id ? 'child-of-' . $model->parent_id . ' ' : '';
    $levelAttr = ' data-level="' . $model->level . '"';

    $cols = count($this->columns);
    echo '<tr style="display:none;" class="before" id="before-', $model->getPrimaryKey(), '">';
    for ($i = 0; $i < $cols; $i++)
      echo '<td style="padding:0;"><div style="height:3px;"></div></td>';
    echo '</tr>', PHP_EOL;

    if ($this->rowCssClassExpression !== null) {
      echo '<tr id="' . $model->getPrimaryKey() . '" class="' . $parentClass . $this->evaluateExpression($this->rowCssClassExpression, array('row' => $row, 'data' => $model)) . '"', $levelAttr, '>';
    } else if (is_array($this->rowCssClass) && ($n = count($this->rowCssClass)) > 0)
      echo '<tr id="' . $model->getPrimaryKey() . '" class="' . $parentClass . $this->rowCssClass[$row % $n] . '"', $levelAttr, '>';
    else
      echo '<tr id="' . $model->getPrimaryKey() . '" class="' . $parentClass . '"', $levelAttr, '>';
    foreach ($this->columns as $column) {
      $column->renderDataCell($row);
    }

    echo '</tr>', PHP_EOL;
    echo '<tr style="display:none;" class="after" id="after-', $model->getPrimaryKey(), '">';
    for ($i = 0; $i < $cols; $i++)
      echo '<td style="padding:0;"><div style="height:3px;"></div></td>';
    echo '</tr>', PHP_EOL;
  }

}
