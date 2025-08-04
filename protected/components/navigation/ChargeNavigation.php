<?php

/**
 * Description of ChargeNavigation
 *
 * @author francesco.colamonici
 */
class ChargeNavigation extends TaskNavigation {
    protected static $chargeTypes = array('my', 'all');
    
    public static function setChargeType($type) {
        $navigation = self::getNavigation();
        if (in_array($type, self::$chargeTypes))
            $navigation['charge']['type'] = $type;
        else
            throw new DomainException('Invalid Charge Type: ' . $type);
        Yii::app()->user->setState('navigation', $navigation);
    }

    public static function getChargeType() {
        $navigation = self::getNavigation();
        return $navigation['charge']['type'];
    }

    public static function clearChargeType() {
        $navigation = self::getNavigation();
        $navigation['charge']['type'] = '';
        Yii::app()->user->setState('navigation', $navigation);
    }
}
