<?php

/**
 * Description of RWebUser
 *
 * @author francesco.colamonici
 */
class RWebUser extends CWebUser {

  public function checkAccess($operation, $params = array(), $allowCaching = true) {
    $paramId = md5(serialize($params));
    $cacheId = $this->getId() . $operation . $paramId;

    if ($allowCaching && $params === array() && Yii::app()->cache->get($cacheId) !== false)
      return Yii::app()->cache->get($cacheId) === 1;

    $checkAccess = Yii::app()->getAuthManager()->checkAccess($operation, $this->getId(), $params);

    if ($allowCaching && !$this->getIsGuest())
      Yii::app()->cache->set($cacheId, $checkAccess ? 1 : 0);

    return $checkAccess;
  }

}

?>
