<?php

/**
 * Description of Navigation
 *
 * @author francesco.colamonici
 */
class Navigation {

    protected static $navigation = array(
        'home' => false,
        'project' => array('type' => 'my', 'id' => ''),
        'task' => array('type' => 'my', 'id' => '')
    );

    protected static function getNavigation() {
        return Yii::app()->user->getState('navigation', self::$navigation);
    }

    public static function home($flag = null) {
        $navigation = self::getNavigation();
        if ($flag === null)
            return $navigation['home'];
        if (is_bool($flag)) {
            $navigation['home'] = $flag;
            Yii::app()->user->setState('navigation', $navigation);
        }
    }

}
