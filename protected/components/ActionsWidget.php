<?php

Yii::import('system.web.widgets.CWidget');

/**
 * Description of ActionsWidget
 *
 * @author francesco.colamonici
 */
class ActionsWidget extends CWidget {

  /**
   * @var string the tag name for the view container. Defaults to 'span'.
   */
  public $tagName = 'span';

  /**
   * @var array the HTML options for the view container tag.
   */
  public $htmlOptions = array('class' => 'actions');

  /**
   * @var string the template that is used to render the content in each data cell.
   * These default tokens are recognized: {view}, {update} and {delete}. If the {@link buttons} property
   * defines additional buttons, their IDs are also recognized here. For example, if a button named 'preview'
   * is declared in {@link buttons}, we can use the token '{preview}' here to specify where to display the button.
   */
  public $template = '{create} {update} {delete}';

  /**
   * @var string the label for the view button. Defaults to "View".
   * Note that the label will not be HTML-encoded when rendering.
   */
  public $createButtonLabel;

  /**
   * @var string the image URL for the view button. If not set, an integrated image will be used.
   * You may set this property to be false to render a text link instead.
   */
  public $createButtonImageUrl;

  /**
   * @var string a PHP expression that is evaluated for every create button and whose result is used
   * as the URL for the view button. In this expression, the variable
   * <code>$row</code> the row number (zero-based); <code>$data</code> the data model for the row;
   * and <code>$this</code> the column object.
   */
  public $createButtonUrl = 'Yii::app()->controller->createUrl($this->entity . "/create")';
  public $createButtonVisible = 'Yii::app()->user->checkAccess("create" . ucfirst($this->entity))';

  /**
   * @var array the HTML options for the view button tag.
   */
  public $createButtonOptions = array('class' => 'create');

  /**
   * @var string the label for the update button. Defaults to "Update".
   * Note that the label will not be HTML-encoded when rendering.
   */
  public $updateButtonLabel;

  /**
   * @var string the image URL for the update button. If not set, an integrated image will be used.
   * You may set this property to be false to render a text link instead.
   */
  public $updateButtonImageUrl;

  /**
   * @var string a PHP expression that is evaluated for every update button and whose result is used
   * as the URL for the update button. In this expression, the variable
   * <code>$row</code> the row number (zero-based); <code>$data</code> the data model for the row;
   * and <code>$this</code> the column object.
   */
  public $updateButtonUrl = 'Yii::app()->controller->createUrl($this->entity . "/update", array("id"=>$data->primaryKey))';
  public $updateButtonVisible = 'Yii::app()->user->checkAccess("update" . ucfirst($this->entity))';

  /**
   * @var array the HTML options for the update button tag.
   */
  public $updateButtonOptions = array('class' => 'update');

  /**
   * @var string the label for the delete button. Defaults to "Delete".
   * Note that the label will not be HTML-encoded when rendering.
   */
  public $deleteButtonLabel;

  /**
   * @var string the image URL for the delete button. If not set, an integrated image will be used.
   * You may set this property to be false to render a text link instead.
   */
  public $deleteButtonImageUrl;

  /**
   * @var string a PHP expression that is evaluated for every delete button and whose result is used
   * as the URL for the delete button. In this expression, the variable
   * <code>$row</code> the row number (zero-based); <code>$data</code> the data model for the row;
   * and <code>$this</code> the column object.
   */
  public $deleteButtonUrl = 'Yii::app()->controller->createUrl($this->entity . "/delete", array("id"=>$data->primaryKey))';
  public $deleteButtonVisible = 'Yii::app()->user->checkAccess("delete" . ucfirst($this->entity))';

  /**
   * @var array the HTML options for the delete button tag.
   */
  public $deleteButtonOptions = array('class' => 'delete');

  /**
   * @var string the confirmation message to be displayed when delete button is clicked.
   * By setting this property to be false, no confirmation message will be displayed.
   * This property is used only if <code>$this->buttons['delete']['click']</code> is not set.
   */
  public $deleteConfirmation;

  /**
   * @var string a PHP expression that is evaluated for every delete button and whose result is used
   * as the URL for the delete button. In this expression, the variable
   * <code>$row</code> the row number (zero-based); <code>$data</code> the data model for the row;
   * and <code>$this</code> the column object.
   */
  public $deleteRedirectUrl = 'Yii::app()->controller->createUrl("index")';
  private $buttons = array();

  /**
   * @var CModel Model to use.
   */
  public $data;
  public $entity;

  /**
   * Initializes the view.
   * This method will initialize required property values and instantiate {@link columns} objects.
   */
  public function init() {
    if ($this->entity === null)
      $this->entity = lcfirst(get_class($this->data));

    $this->initDefaultButtons();

    foreach ($this->buttons as $id => $button) {
      if (strpos($this->template, '{' . $id . '}') === false)
        unset($this->buttons[$id]);
      elseif (isset($button['click'])) {
        if (!isset($button['options']['class']))
          $this->buttons[$id]['options']['class'] = $id;
        if (!($button['click'] instanceof CJavaScriptExpression))
          $this->buttons[$id]['click'] = new CJavaScriptExpression($button['click']);
      }
    }

    $this->htmlOptions['id'] = $this->getId();
  }

  /**
   * Renders the view.
   * This is the main entry of the whole view rendering.
   * Child classes should mainly override {@link renderContent} method.
   */
  public function run() {
    $content = $this->renderContent();
    if (preg_replace('/\s+/', '', $content) !== '') {
      $this->registerClientScript();

      echo CHtml::openTag($this->tagName, $this->htmlOptions) . PHP_EOL;

      echo $content;

      echo CHtml::closeTag($this->tagName);
      echo CHtml::tag('div', array('style' => 'clear:both;height:.5em;'), '&nbsp;');
    }
  }

  /**
   * Initializes the default buttons (view, update and delete).
   */
  protected function initDefaultButtons() {
    if ($this->createButtonLabel === null)
      $this->createButtonLabel = Yii::t('nav', 'Create');
    if ($this->updateButtonLabel === null)
      $this->updateButtonLabel = Yii::t('zii', 'Update');
    if ($this->deleteButtonLabel === null)
      $this->deleteButtonLabel = Yii::t('zii', 'Delete');
    if ($this->createButtonImageUrl === null)
      $this->createButtonImageUrl = Yii::app()->baseUrl . '/images/actions/database_add.png';
    if ($this->updateButtonImageUrl === null)
      $this->updateButtonImageUrl = Yii::app()->baseUrl . '/images/actions/database_edit.png';
    if ($this->deleteButtonImageUrl === null)
      $this->deleteButtonImageUrl = Yii::app()->baseUrl . '/images/actions/database_delete.png';
    if ($this->deleteConfirmation === null)
      $this->deleteConfirmation = Yii::t('zii', 'Are you sure you want to delete this item?');

    foreach (array('create', 'update', 'delete') as $id) {
      $button = array(
          'label' => $this->{$id . 'ButtonLabel'},
          'url' => $this->{$id . 'ButtonUrl'},
          'imageUrl' => $this->{$id . 'ButtonImageUrl'},
          'options' => $this->{$id . 'ButtonOptions'},
          'visible' => $this->{$id . 'ButtonVisible'},
      );
      if (isset($this->buttons[$id]))
        $this->buttons[$id] = array_merge($button, $this->buttons[$id]);
      else
        $this->buttons[$id] = $button;
    }

    if (!isset($this->buttons['delete']['click'])) {
      if (is_string($this->deleteConfirmation))
        $confirmation = 'if(!confirm(' . CJavaScript::encode($this->deleteConfirmation) . ')) return false;';
      else
        $confirmation = '';

      if (Yii::app()->request->enableCsrfValidation) {
        $csrfTokenName = Yii::app()->request->csrfTokenName;
        $csrfToken = Yii::app()->request->csrfToken;
        $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
      } else
        $csrf = '';

//            if ($this->afterDelete === null)
//                $this->afterDelete = 'function(){}';

      $this->buttons['delete']['click'] = <<<EOD
function() {
    $confirmation
    jQuery(this).on('click', $.ajax({
            type: 'POST',
            url: jQuery(this).attr('href'),$csrf
            success: function(data) {
                window.location = '{$this->evaluateExpression($this->deleteRedirectUrl, array('data' => $this->data))}';
            },
        })
    );
    return false;
}
EOD;
    }
  }

  /**
   * Registers the client scripts for the button column.
   */
  protected function registerClientScript() {
    $js = array();
    foreach ($this->buttons as $id => $button) {
      if (isset($button['click'])) {
        $function = CJavaScript::encode($button['click']);
        $class = preg_replace('/\s+/', '.', $button['options']['class']);
        $js[] = "jQuery(document).on('click','#{$this->getId()} a.{$class}', $function);";
      }
    }

    if ($js !== array())
      Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->getId(), implode(PHP_EOL, $js));
  }

  /**
   * Renders the main content of the view.
   * The content is divided into sections, such as summary, items, pager.
   * Each section is rendered by a method named as "renderXyz", where "Xyz" is the section name.
   * The rendering results will replace the corresponding placeholders in {@link template}.
   */
  public function renderContent() {
    $tr = array();
    ob_start();
    foreach ($this->buttons as $id => $button) {
      $this->renderButton($id, $button, $this->data);
      $tr['{' . $id . '}'] = ob_get_contents();
      ob_clean();
    }
    ob_end_clean();
    return strtr($this->template, $tr);
  }

  /**
   * Renders a link button.
   * @param string $id the ID of the button
   * @param array $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
   * See {@link buttons} for more details.
   * @param mixed $data the data object associated with the row
   */
  protected function renderButton($id, $button, $data) {
    if (isset($button['visible']) && !$this->evaluateExpression($button['visible'], array('data' => $data)))
      return;
    $label = isset($button['label']) ? $button['label'] : $id;
    $url = isset($button['url']) ? $this->evaluateExpression($button['url'], array('data' => $data)) : '#';
    $options = isset($button['options']) ? $button['options'] : array();
    if (!isset($options['title']))
      $options['title'] = $label;
    if (isset($button['imageUrl']) && is_string($button['imageUrl']))
      echo CHtml::link($label . CHtml::image($button['imageUrl'], $label), $url, $options);
    else
      echo CHtml::link($label, $url, $options);
  }

}
