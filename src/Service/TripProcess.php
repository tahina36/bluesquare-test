<?php
namespace App\Service;

use App\Entity\Trip;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TripProcess {
    private $hasError = false;
    private $messages = [];

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function isHasError(): bool
    {
        return $this->hasError;
    }

    /**
     * @param bool $hasError
     */
    public function setHasError(bool $hasError): void
    {
        $this->hasError = $hasError;
    }

    /**
     * @param array $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /*
     * set datas for each step of the trip
     */
    private function setSteps(Trip $trip)
    {
        $collection = new ArrayCollection();
        foreach ($trip->getSteps() as $key => $step) {
            $tmp = clone $step;
            $tmp->setStep($key);
            $tmp->setTrip($trip);
            $collection->add($tmp);
        }
        $trip->setSteps($collection);
    }

    private function checkDates($step, $key)
    {
        if (!$step->getDepartureDate()) {
            $this->hasError = true;
            $this->messages[] = sprintf("Erreur à l'etape %s : La date de départ est obligatoire", $key+1);
        }
        if (!$step->getArrivalDate()) {
            $this->hasError = true;
            $this->messages[] = sprintf("Erreur à l'etape %s : La date d'arrivée est obligatoire", $key+1);
        }
        if ($step->getDepartureDate() && !($step->getDepartureDate() instanceof DateTime)) {
            $this->hasError = true;
            $this->messages[] = sprintf("Erreur à l'etape %s : La date de départ est invalide", $key+1);
        }
        if ($step->getArrivalDate() && !($step->getArrivalDate() instanceof DateTime)) {
            $this->hasError = true;
            $this->messages[] = sprintf("Erreur à l'etape %s : La date d'arrivée est invalide", $key+1);
        }
    }

    private function checkErrors(Trip $trip)
    {
        if ($trip->getSteps()->isEmpty()) {
            $this->messages[] = "Le voyage doit contenir au moins une étape";
            $this->hasError = true;
        }
    }

    /*
     * check errors related to the itinerary itself
     */
    private function checkItinerary(Trip $trip)
    {
        $this->checkIfDepartureAndArrivalDifferents($trip);
    }

    /*
     * check if the departure and arrival of the step are the same or not
     */
    private function checkIfDepartureAndArrivalDifferents(Trip $trip)
    {
        foreach ($trip->getSteps() as $key => $step) {
            if (($step->getDeparture() != "" && $step->getArrival() != "") && ($step->getDeparture() == $step->getArrival())) {
                $this->hasError = true;
                $this->messages[] = sprintf("Erreur à l'etape %s : La ville de départ et d'arrivée doivent etre différents", $key+1);
            }
        }
        if ($this->hasError) {
            return false;
        }
        return true;
    }

    private function checkIfEmptyStep(Trip $trip) {
        foreach ($trip->getSteps() as $key => $step) {
            if (!$step->getVehicule() && in_array($step->getVehicule()->getType(), ['train', 'bus'])) {
                if (!$step->getVehicule()->getSeat() && !$step->getVehicule()->getNumber() && !$step->getDeparture() && !$step->getArrival()) {
                    $this->hasError = true;
                    $this->messages[] = sprintf("Erreur à l'etape %s : L'étape ne peut etre vide", $key+1);
                }
            }
            elseif (!$step->getVehicule() && in_array($step->getVehicule()->getType(), ['plane'])) {
                if (!$step->getVehicule()->getSeat() && !$step->getVehicule()->getNumber() && !$step->getVehicule()->getGate() && !$step->getVehicule()->getBagageDrop()
                    && !$step->getDeparture() && !$step->getArrival()) {
                    $this->hasError = true;
                    $this->messages[] = sprintf("Erreur à l'etape %s : L'étape ne peut etre vide", $key+1);
                }
            }
        }
    }

    private function checkIfValidType(Trip $trip) {
        foreach ($trip->getSteps() as $key => $step) {
            if (!$step->getVehicule() || $step->getVehicule() && !in_array($step->getVehicule()->getType(), ['plane', 'train', 'bus'])) {
                $this->hasError = true;
                $this->messages[] = sprintf("Erreur à l'etape %s : Le mode de transport n'est pas valide", $key+1);
            }
        }
    }

    private function checkIfInvalidDatas(Trip $trip) {
        if (!$trip->getName()) {
            $this->hasError = true;
            $this->messages[] = sprintf("Le nom du voyage est obligatoire");
        }
        foreach ($trip->getSteps() as $key => $step) {
            if (!$step->getVehicule() || $step->getVehicule() && !in_array($step->getVehicule()->getType(), ['plane', 'train', 'bus'])) {
                $this->hasError = true;
                $this->messages[] = sprintf("Erreur à l'etape %s : Le mode de transport n'est pas valide", $key+1);
            }
            else {
                $this->checkDates($step, $key);
                if (!$step->getDeparture()) {
                    $this->hasError = true;
                    $this->messages[] = sprintf("Erreur à l'etape %s : La ville de départ est obligatoire", $key+1);
                }
                if (!$step->getArrival()) {
                    $this->hasError = true;
                    $this->messages[] = sprintf("Erreur à l'etape %s : La ville d'arrivée est obligatoire", $key+1);
                }
                if ($step->getVehicule()) {
                    if (in_array($step->getVehicule()->getType(), ['plane']) && $step->getVehicule()->getBagageDrop()) {
                        if (!is_numeric($step->getVehicule()->getBagageDrop()) || $step->getVehicule()->getBagageDrop() < 0) {
                            $this->hasError = true;
                            $this->messages[] = sprintf("Erreur à l'etape %s : Le nombre de bagage doit etre un entier positif", $key+1);
                        }
                    }
                }
            }
        }
    }

    /*
     * process Trip : check if there are any errors, if not, we set remaining datas
     */
    public function process(Trip &$trip) {
        $this->checkErrors($trip);
        if ($this->isHasError()) {
            return false;
        }
        $this->checkIfValidType($trip);
        if ($this->isHasError()) {
            return false;
        }
        $this->checkIfInvalidDatas($trip);
        if ($this->isHasError()) {
            return false;
        }
        $this->checkIfEmptyStep($trip);
        if ($this->isHasError()) {
            return false;
        }
        $this->checkItinerary($trip);
        if ($this->isHasError()) {
            return false;
        }
        $this->setSteps($trip);
        $trip->setCreatedAt(new \DateTime());
        $trip->generateSlug();
        return true;
    }
}
?>