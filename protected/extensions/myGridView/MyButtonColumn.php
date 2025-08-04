<?php

Yii::import('zii.widgets.grid.CButtonColumn');

/**
 * Description of MyButtonColumn
 *
 * @author francesco.colamonici
 */
class MyButtonColumn extends CButtonColumn {

  public function init() {
    $this->header = Yii::t('app', 'Form.action');
    $this->viewButtonImageUrl = Yii::app()->baseUrl . '/images/actions/database_table.png';
    $this->updateButtonImageUrl = Yii::app()->baseUrl . '/images/actions/database_edit.png';
    $this->deleteButtonImageUrl = Yii::app()->baseUrl . '/images/actions/database_delete.png';
    parent::init();
  }

  public function initDefaultButtons() {
    parent::initDefaultButtons();

    if (!isset($this->buttons['delete']['classicModal'])) {
      if (is_string($this->deleteConfirmation))
        $confirmation = CJavaScript::encode($this->deleteConfirmation);
      else
        $confirmation = '';

      if (Yii::app()->request->enableCsrfValidation) {
        $csrfTokenName = Yii::app()->request->csrfTokenName;
        $csrfToken = Yii::app()->request->csrfToken;
        $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
      } else
        $csrf = '';

      $deleteTitle = Yii::t('nav', 'Delete Confirmation');

      $this->buttons['delete']['click'] = <<<EOD
				function(e) {
				e.preventDefault();
				var ajaxUrl = jQuery(this).attr('href');
				$("body").append( '<div id="dialog-confirm"></div>' );
				console.log( $("#dialog-confirm") );
				$("#dialog-confirm").html(eval("$confirmation"));
				$("#dialog-confirm").dialog({
					resizable: false,
					modal: true,
                    title: "{$deleteTitle}",
					height: 250,
					width: 400,
					draggable: false,
					dialogClass: "no-close",
					buttons: {
						"Yes": function () {
							var th = this,
							afterDelete = $this->afterDelete;
							jQuery('#{$this->grid->id}').yiiGridView('update', {
							type: 'POST',
							url: ajaxUrl,$csrf
							success: function(data) {
							jQuery('#{$this->grid->id}').yiiGridView('update');
							afterDelete(th, true, data);
							},
							error: function(XHR) {
							return afterDelete(th, false, XHR);
							}
							});
							
							$(this).dialog('close');
						},
						"No": function () {
							$(this).dialog('close');
						}
					}
				});
				
				return false;
				}
EOD;
    }
  }

  public function getFilterCellContent() {
    $img = CHtml::image(Yii::app()->baseUrl . '/images/filterRemove.png', Yii::t('app', 'Filter.query.cancel'));
    $a = CHtml::link($img, '#', array('class' => 'query-cancel'));

    if (Yii::app()->request->enableCsrfValidation) {
      $csrfTokenName = Yii::app()->request->csrfTokenName;
      $csrfToken = Yii::app()->request->csrfToken;
      $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
    } else
      $csrf = '';

    $script = <<<EOD
      jQuery(document).on("click.yiiGridView", ".grid-view .query-cancel",
        function(e) {
          e.preventDefault();
          var id = "{$this->grid->id}", filterSelector = "{$this->grid->filterSelector}", filterClass = "filters",
              inputSelector = '#' + id + ' .' + filterClass + ' input, ' + '#' + id + ' .' + filterClass + ' select';
          filterSelector = filterSelector.replace('{filter}', inputSelector);
          jQuery(":input", ".filters")
            .not(":button, :submit, :reset")
            .val("")
            .removeAttr("checked")
            .removeAttr("selected");
          jQuery("option", ".filters").removeAttr("selected");

          var data = $(inputSelector).serialize();
          jQuery('#' + id).yiiGridView("update", {data: data});
        }
      );
EOD;
    Yii::app()->clientScript->registerScript('query-cancel-action', $script, CClientScript::POS_READY);
    return $a;
  }

}
