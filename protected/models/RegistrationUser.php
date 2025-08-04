<?php

class RegistrationUser extends CActiveRecord
{

    public $password_repeat; // パスワード確認用の属性

    // テーブル名を指定
    public function tableName()
    {
        return 'myt_user';
    }

    // バリデーションルール
    public function rules()
    {
        return array(
            array('username, password, email, gender', 'required'),
            array('username, email', 'unique'),
            array('email', 'email'),
            array('password', 'length', 'min' => 6, 'max' => 64),
            array('confirm_key', 'length', 'max' => 40),
            array('confirm_key, active', 'safe'),  // confirm_key と active を safe にする
            array('password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords must match.'), // パスワードの一致を確認
            // その他のバリデーションルール
        );
    }

    // 関連（例としてProfileモデルとの関連）
    public function relations()
    {
        return array(
            'profile' => array(self::BELONGS_TO, 'Profile', 'profile_id'),
        );
    }

    // 属性ラベル
    public function attributeLabels()
    {
        return array(
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'gender' => 'Gender',
            'confirm_key' => 'Confirmation Key',
        );
    }

    // レコードの検索条件
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('username', $this->username, true);
        $criteria->compare('email', $this->email, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    // モデルのインスタンスを返す
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

/*
    // パスワードをハッシュ化する
    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord || $this->password !== $this->getOldAttribute('password'))     
            {
                // パスワードをbcryptでハッシュ化する
                $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            }

            if ($this->isNewRecord)
            {
                $this->created = new CDbExpression('NOW()'); // 現在の日時を設定
                $this->last_upd = new CDbExpression('NOW()');


                // active フィールドを 0 に設定
                $this->active = 0;            }

                return true;
            } else {
                return false;
            }
        }
        */

/*
    protected function beforeSave()
    {
	    if (parent::beforeSave()) {
	        // 新規レコードまたはパスワードが変更された場合に処理を実行
	        if ($this->isNewRecord || $this->isAttributeChanged('password')) {
	            // パスワードをbcryptでハッシュ化する
	            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
	        }

	        if ($this->isNewRecord) {
	            $this->created = new CDbExpression('NOW()'); // 現在の日時を設定
	            $this->last_upd = new CDbExpression('NOW()'); // 更新日時を設定

	            // active フィールドを 0 に設定
	            $this->active = 0;
	        }

	        return true;
	    } else {
	        return false;
	    }
    }
*/

private $oldPassword;

protected function beforeSave()
{
    if (parent::beforeSave()) {
        if ($this->isNewRecord) {
            $this->created = new CDbExpression('NOW()');
            $this->last_upd = new CDbExpression('NOW()');
            $this->active = 0;
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        } else {
            // 既存レコードの場合、パスワードが変更されたかを確認
            if ($this->oldPassword !== $this->password) {
                // パスワードをbcryptでハッシュ化する
                $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            }
        }

        return true;
    } else {
        return false;
    }
}
    
    public function afterFind()
{
    parent::afterFind();
    // データベースから読み込んだパスワードを保存
    $this->oldPassword = $this->password;
}
    
    
}
