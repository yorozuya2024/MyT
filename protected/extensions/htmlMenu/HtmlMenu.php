<?php

Yii::import('zii.widgets.CMenu');

/**
 * Description of HtmlMenu
 * @link http://handy-html.com/wp-content/themes/handyhtmlv2/uploads/tutorial-files/cross-browser-compatible-dropdown-menu.html demo
 *
 * @author francesco.colamonici
 */
class HtmlMenu extends CMenu {

  private $baseUrl;
  public $cssFile;

  /**
   * Initialize the widget
   */
  public function init() {
    if (!$this->getId(false))
      $this->setId('nav');

    $this->items = $this->cssLastItems($this->items);

    parent::init();
  }

  /**
   * Run the widget
   */
  public function run() {
    $this->publishAssets();
    $this->registerClientScripts();
    $this->registerCssFile($this->cssFile);
    $htmlOptions['id'] = 'nav-container';
    echo CHtml::openTag('div', $htmlOptions) . PHP_EOL;
    $htmlOptions['id'] = 'nav-bar';
    echo CHtml::openTag('div', $htmlOptions) . PHP_EOL;
    parent::run();
    echo CHtml::closeTag('div');
    echo CHtml::closeTag('div');
  }

  /**
   * Give the last items css 'last' style
   */
  protected function cssLastItems($items) {
    $keys = array_keys($items);
    if (!empty($keys)) {
      $i = max($keys);
      $item = $items[$i];

      if (isset($item['itemOptions']['class']))
        $items[$i]['itemOptions']['class'] .= ' last';
      else
        $items[$i]['itemOptions']['class'] = 'last';

      foreach ($items as $i => $item) {
        if (isset($item['items'])) {
          $items[$i]['items'] = $this->cssLastItems($item['items']);
        }
      }
    }
    return array_values($items);
  }

  /**
   * Publishes the assets
   */
  public function publishAssets() {
    $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    $this->baseUrl = Yii::app()->getAssetManager()->publish($dir);
  }

  /**
   * Registers the external javascript files
   */
  public function registerClientScripts() {
    // add the script
    $cs = Yii::app()->getClientScript();
    $cs->registerCoreScript('jquery');

    $js = array();
    $js[] = "var right = '<div class=\'right arrow-right\'></div>';";
    $js[] = "var down = '<div class=\'right arrow-down\'></div>';";
    $js[] = "jQuery('#{$this->getId()} ul span').parent().addClass('parent');";
    $js[] = "jQuery('#{$this->getId()} span').each(function() {";
    $js[] = "  if (jQuery(this).parent().is('.parent'))";
    $js[] = "    jQuery(this).append(jQuery(right));";
    $js[] = "  else";
    $js[] = "    jQuery(this).append(jQuery(down));";
    $js[] = "});";
    $cs->registerScript('htmlMenu_' . $this->getId(), implode(PHP_EOL, $js), CClientScript::POS_READY);
  }

  public function registerCssFile($url = null) {
    $cs = Yii::app()->getClientScript();
    if ($url === null) {
      $url = $this->baseUrl . '/style.css';
      $cs->registerCssFile($url, 'screen');
    } else {
      $cs->registerCssFile($url, 'screen');
    }
  }

}
