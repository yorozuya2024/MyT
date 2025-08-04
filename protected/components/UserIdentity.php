<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

  private $_id;
  private $_name;
  private $_avatar;
  private $_gender;

  public function authenticate() {
    $user = User::model(null, true)->active()->findByAttributes(array('username' => $this->username));

    if ($user === null) {
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    } else if ($user->password !== crypt($this->password, $user->password)) {
      $this->errorCode = self::ERROR_PASSWORD_INVALID;
    } else {
      $this->_id = $user->id;
      $this->_name = $user->calc_name === null ? $user->username : $user->calc_name;
	  $this->_gender = $user->gender;
	  $this->_avatar = $user->avatar ? 
					   Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . $user->avatar :
					   Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . 'default_avatar_' . $this->_gender . '.jpg';
	  
	  
      /**
       * @link http://www.yiiframework.com/wiki/6/how-to-add-more-information-to-yii-app-user/
       * How to add more information to Yii App User
       */
      $pU = $user->page_size ? $user->page_size : 0;
      $pA = Yii::app()->params['pageSize'] ? Yii::app()->params['pageSize'] : 10;
      $this->setState('pageSize', intval($pU) < 1 ? intval($pA) : intval($pU));
      $this->setState('username', $user->username);
	  $this->setState('avatar', $this->_avatar);
      $this->errorCode = self::ERROR_NONE;
    }

    return !$this->errorCode;
  }

  public function getId() {
    return $this->_id;
  }

  public function getName() {
    return $this->_name;
  }

}
