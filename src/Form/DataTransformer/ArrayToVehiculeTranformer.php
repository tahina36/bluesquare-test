<?php
namespace App\Form\DataTransformer;

use App\Entity\Vehicule;
use App\Service\Helper;
use Symfony\Component\Form\DataTransformerInterface;

class ArrayToVehiculeTranformer implements DataTransformerInterface
{

    public function transform($vehicule) : array
    {
        return [];
    }

    public function reverseTransform($vehiculeArray): ?Vehicule
    {
        return Helper::getVehiculeFromArray($vehiculeArray);
    }
}