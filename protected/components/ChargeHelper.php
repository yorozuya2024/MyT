<?php

class ChargeHelper {

  public static $dayoftheweek_format = '%a';

  //2025/08/03 add
  // PHP 8.1+ の DateTime::format 用
  //public static $dayoftheweek_format_php = 'l';
  public static $dayoftheweek_format_php = 'D';

  public static function generateQuindicina($day = NULL, $month = NULL, $year = NULL) {
    static $quindicina = array();

    $start = 1;
    $end = 15;

    if ($month === NULL) {
      $month = date('m');
    }

    if ($year === NULL) {
      $year = date('Y');
    }

    if ($day === NULL) {
      $day = date('d');
    }

    if ($day > 15) {
      $start = 16;
      $end = date('t', mktime(1, 0, 0, $month, 1, $year));
    }

    for ($i = $start; $i <= $end; $i++) {
      //2025/08/03 modified
      //$quindicina[$i] = strftime(self::$dayoftheweek_format, strtotime($i . '-' . $month . '-' . $year));
    
      $dateStr = sprintf('%02d-%02d-%04d', $i, $month, $year);
      $date = DateTime::createFromFormat('d-m-Y', $dateStr);

      if ($date !== false) {
          $quindicina[$i] = $date->format(self::$dayoftheweek_format_php);
      }

    }

    return $quindicina;
  }

  public static function generateMonth($month = NULL, $year = NULL) {
    return self::generateQuindicina(1, $month, $year) + self::generateQuindicina(16, $month, $year);
  }

  public static function getQuindicinaAlias($day) {
    return $day > 15 ? 'second_quindicina' : 'first_quindicina';
  }

  public static function getWe($dayofWeek) {
    return $dayofWeek === 'Sat' || $dayofWeek === 'Sun' ? 'we' : 'normal';
  }

  public static function getCSSClass($day, $dayofWeek) {
    return self::getQuindicinaAlias($day) . ' ' . self::getWe($dayofWeek);
  }

  public static function getCurrentQuindicina() {
    return self::getQuindicina(date('d'), date('m'), date('Y'));
  }

  public static function getQuindicina($day, $month, $year) {
    $d = $day > 15 ? date('t', mktime(1, 0, 0, $month, $day, $year)) : 15;

    return $d . '/' . $month . '/' . $year;
  }

  public static function getLastDayOfQuindicina($day, $month, $year) {
    return substr(self::getQuindicina($day, $month, $year), 0, 2);
  }

  public static function getListPrevQuindicine($goBackMonths) {
    $arr = array();

    $arr[] = self::getCurrentQuindicina();

    if (date('d') > 15) {
      $arr[] = self::getQuindicina(1, date('m'), date('Y'));
    }

    for ($i = 1; $i <= $goBackMonths; $i++) {
      $arr[] = date('t/m/Y', strtotime("-$i months"));
      $arr[] = '15/' . date('m/Y', strtotime("-$i months"));
    }

    return array_combine(array_values($arr), array_values($arr));
  }

  public static function getNextQuindicina($date) {
    $arr = explode('/', $date);

    $time = strtotime("{$arr[1]}/{$arr[0]}/{$arr[2]}");

    if ($arr[0] > 15) {
      return '15/' . date('m/Y', strtotime("+28 days", $time)); // for short months
    } else {
      return date('t/m/Y', $time);
    }
  }

  public static function getPrevQuindicina($date) {
    $arr = explode('/', $date);

    $time = strtotime("{$arr[1]}/{$arr[0]}/{$arr[2]}");

    if ($arr[0] <= 15) {
      return date('t/m/Y', strtotime("-1 months", $time));
    } else {
      return '15/' . date('m/Y', $time);
    }
  }

  public static function formatDBDateForGUI($dateStr) {
    return implode('/', array_reverse(explode('-', $dateStr)));
  }

}
