<?php

/**
 * Description of ELocalizedFormatter
 *
 * @author francesco.colamonici
 */
class ELocalizedFormatter extends CLocalizedFormatter {

  /**
   * Formats the value as a boolean.
   * @param mixed $value the value to be formatted
   * @return string the formatted result
   * @see booleanFormat
   */
  public function formatBoolean($value) {
    return $value ? Yii::t('app', $this->booleanFormat[1]) : Yii::t('app', $this->booleanFormat[0]);
  }

}
