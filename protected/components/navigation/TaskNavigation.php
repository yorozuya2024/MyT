<?php

/**
 * Description of TaskNavigation
 *
 * @author francesco.colamonici
 */
class TaskNavigation extends ProjectNavigation {

    protected static $taskTypes = array('my', 'all');

    public static function setTaskType($type) {
        $navigation = self::getNavigation();
        if (in_array($type, self::$taskTypes))
            $navigation['task']['type'] = $type;
        else
            throw new DomainException('Invalid Task Type: ' . $type);
        Yii::app()->user->setState('navigation', $navigation);
    }

    public static function getTaskType() {
        $navigation = self::getNavigation();
        return $navigation['task']['type'];
    }

    public static function clearTaskType() {
        $navigation = self::getNavigation();
        $navigation['task']['type'] = '';
        Yii::app()->user->setState('navigation', $navigation);
    }

    public static function setTaskId($id) {
        $navigation = self::getNavigation();
        $navigation['task']['id'] = $id;
        Yii::app()->user->setState('navigation', $navigation);
    }

    public static function getTaskId() {
        $navigation = self::getNavigation();
        return $navigation['task']['id'];
    }

    public static function clearTaskId() {
        $navigation = self::getNavigation();
        $navigation['task']['id'] = '';
        Yii::app()->user->setState('navigation', $navigation);
    }

}
