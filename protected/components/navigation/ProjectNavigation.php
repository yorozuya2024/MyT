<?php

/**
 * Description of ProjectNavigation
 *
 * @author francesco.colamonici
 */
class ProjectNavigation extends Navigation {

    protected static $projectTypes = array('my', 'all');

    public static function setProjectType($type) {
        $navigation = self::getNavigation();
        if (in_array($type, self::$projectTypes))
            $navigation['project']['type'] = $type;
        else
            throw new DomainException('Invalid Project Type: ' . $type);
        Yii::app()->user->setState('navigation', $navigation);
    }

    public static function getProjectType() {
        $navigation = self::getNavigation();
        return $navigation['project']['type'];
    }

    public static function clearProjectType() {
        $navigation = self::getNavigation();
        $navigation['project']['type'] = '';
        Yii::app()->user->setState('navigation', $navigation);
    }

    public static function setProjectId($id) {
        $navigation = self::getNavigation();
        $navigation['project']['id'] = $id;
        Yii::app()->user->setState('navigation', $navigation);
    }

    public static function getProjectId() {
        $navigation = self::getNavigation();
        return $navigation['project']['id'];
    }

    public static function clearProjectId() {
        $navigation = self::getNavigation();
        $navigation['project']['id'] = '';
        Yii::app()->user->setState('navigation', $navigation);
    }

}
