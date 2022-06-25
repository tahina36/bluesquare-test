<?php
namespace App\Service;

use App\Entity\Bus;
use App\Entity\Plane;
use App\Entity\Train;

class Helper {

    /*
     * Receive a vehicule as an array and returns the correct vehicule
     */
    public static function getVehiculeFromArray($datas) {
        if (!$datas) {
            return null;
        }
        switch ($datas['type']) {
            case 'plane':
                $vehicule = new Plane();
                break;
            case 'bus':
                $vehicule = new Bus();
                break;
            case 'train':
                $vehicule = new Train();
                break;
            default:
                return null;
        }
        if (!empty($datas['number']) && $datas['number'] && method_exists($vehicule, 'setNumber')) {
            $vehicule->setNumber($datas['number']);
        }
        if (!empty($datas['seat']) && $datas['seat'] && method_exists($vehicule, 'setSeat')) {
            $vehicule->setSeat($datas['seat']);
        }
        if (!empty($datas['bagageDrop']) && $datas['bagageDrop'] && method_exists($vehicule, 'setBagageDrop')) {
            $vehicule->setBagageDrop($datas['bagageDrop']);
        }
        if (!empty($datas['gate']) && $datas['gate'] && method_exists($vehicule, 'setGate')) {
            $vehicule->setGate($datas['gate']);
        }
        return $vehicule;
    }

    /*
     * remove all non alphanumerical characters and mutliple spaces (also remove spaces between -) and lower the text
     */
    public static function formatDatas($string) {
        $string = str_replace(' - ', '-', preg_replace('/[\s$@_*]+/', ' ',
            strtolower(preg_replace('/[^0-9a-zA-Z -]/', '',$string))));
        if (strlen(trim($string)) == 0) {
            return null;
        }
        return $string;
    }
}
?>