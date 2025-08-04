<?php

Yii::import('zii.widgets.jui.CJuiDatePicker');

/**
 * Description of EJuiDatePicker
 *
 * @author francesco.colamonici
 */
class EJuiDatePicker extends CJuiDatePicker {

  public $suffix = '_container';
  public $key;

  protected function resolveNameID($nameProperty = 'name', $attributeProperty = 'attribute') {
    list($name, $id) = parent::resolveNameID($nameProperty, $attributeProperty);
    if (strpos($id, $this->key) === false)
      $id = $this->key . $id;
    return array($name, $id);
  }

  public function init() {
    $this->language = Yii::app()->language;

    list($name, $id) = $this->resolveNameID();

    if (isset($this->htmlOptions['id']))
      $id = $this->htmlOptions['id'];
    else
      $this->htmlOptions['id'] = $id;
    if (isset($this->htmlOptions['name']))
      $name = $this->htmlOptions['name'];

    $this->options['altField'] = '#' . $id;
    $this->options['altFormat'] = 'yy-mm-dd';

    if (empty($this->options['onClose'])) {
      $onClose = "function(dateText, datePicker) {if (!dateText) jQuery('{$this->options['altField']}').val('');}";
      $this->options['onClose'] = new CJavaScriptExpression($onClose);
    }

    return parent::init();
  }

  public function getJs() {
    list($name, $id) = $this->resolveNameID();

    if (strpos($id, $this->suffix) === false)
      $id .= $this->suffix;
    $origId = substr($id, 0, strlen($id) - strlen($this->suffix));

    $options = CJavaScript::encode($this->options);
    $js = "jQuery('#{$id}').datepicker($options);";

    if ($this->language != '' && $this->language != 'en') {
      $this->registerScriptFile($this->i18nScriptFile);
      $lang = explode('_', $this->language);
      if (count($lang) === 1)
        $lang = $lang[0];
      else
        $lang = $lang[0] . '-' . strtoupper($lang[1]);
      $js = "jQuery('#{$id}').datepicker(jQuery.extend({showMonthAfterYear:false},jQuery.datepicker.regional['{$lang}'],{$options}));";
    }

    $js .= PHP_EOL . "var newStartDate = jQuery('#{$origId}').val().split('-');";
    $js .= PHP_EOL . "jQuery('#{$id}').datepicker('setDate', newStartDate.length === 3 ? new Date(newStartDate[0], newStartDate[1] - 1, newStartDate[2]) : null);";

    return $js;
  }

  public function run() {
    if ($this->flat || !$this->hasModel())
      return parent::run();

    list($name, $id) = $this->resolveNameID();

    if (isset($this->htmlOptions['id']))
      $id = $this->htmlOptions['id'];
    else
      $this->htmlOptions['id'] = $id;
    if (isset($this->htmlOptions['name']))
      $name = $this->htmlOptions['name'];

    echo CHtml::activeHiddenField($this->model, $this->attribute, array('id' => $id));

    $id = $this->htmlOptions['id'] = $id . $this->suffix;
    $name = $this->htmlOptions['name'] = substr($name, 0, strlen($name) - 1) . $this->suffix . ']';
//    echo CHtml::textField($name, $this->value, $this->htmlOptions);
//    CHtml::addCssClass('filter-calendar', $this->htmlOptions);
    echo CHtml::textField($name, $this->value, $this->htmlOptions);

    $cs = Yii::app()->getClientScript();

    if (isset($this->defaultOptions)) {
      $this->registerScriptFile($this->i18nScriptFile);
      $cs->registerScript(__CLASS__, $this->defaultOptions !== null ? 'jQuery.datepicker.setDefaults(' . CJavaScript::encode($this->defaultOptions) . ');' : '');
    }

    $cs->registerScript(__CLASS__ . '#' . $id, $this->js);
  }

  public function getContent() {
    ob_start();
    ob_implicit_flush(false);
    try {
      $this->run();
    } catch (Exception $e) {
      ob_end_clean();
      throw $e;
    }
    return ob_get_clean();
  }

}
