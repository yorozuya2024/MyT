<?php

/**
 * Description of AndroidNotification
 *
 * @author francesco.colamonici
 */
class AndroidNotification {

  const GOOGLE_API_KEY = 'AIzaSyBGVsJWfsFG3pGXSP6GYBaqPTAbU_0BFQw';
  const GOOGLE_API_URL = 'https://android.googleapis.com/gcm/send';

  public static function sendNotification($userName, array $message, $key = 'pm_message') {
    $result = array();

    $regRows = AndroidUdid::model()->findAllByAttributes(array('user_name' => $userName));
    $regIds = array();
    foreach ($regRows as $regRow)
      array_push($regIds, $regRow->registration_id);
    if (count($regIds) > 0) {
      if (self::_sendNotification($regIds, array($key => $message)))
        $result['success'] = Yii::t('app', 'Android.push.success');
      else
        $result['error'] = Yii::t('app', 'Android.push.failure');
    } else
      $result['notice'] = Yii::t('app', 'Android.push.notice');
    return $result;
  }

  private static function _sendNotification(array $ids, array $message) {
    if (Yii::app()->request->getUserHostAddress() === '127.0.0.1')
      return false;

    $fields = array(
        'registration_ids' => $ids,
        'data' => $message,
    );
    $headers = array(
        'Authorization: key=' . Yii::app()->params['notifications']['googleApiKey'],
        'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, AndroidNotification::GOOGLE_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === false)
      die('Curl failed: ' . curl_error($ch));
    curl_close($ch);
    return $result;
  }

}
