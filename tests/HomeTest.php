<?php

namespace App\Tests;

use App\Entity\Trip;
use App\Entity\TripStep;
use App\Service\Helper;
use App\Service\TripProcess;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class HomeTest extends TestCase
{
    public function testSlugify(): void {
        $trip = new Trip();
        $trip->setName("test voyage");
        $trip->generateSlug();
        $this->assertMatchesRegularExpression('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $trip->getSlug());
    }

    public function testTripValid(): void
    {
        $trips = [
            [
                "name" => "Voyage valide 1B",
                "steps" => [
                    ["type" => "plane", "number" => "SK22", "departure" => "Stockholm", "arrival" => "New York JFK", "seat" => "7B", "gate" => "22",
                        "departureDate" => new \DateTime('2022-02-02'), "arrivalDate" => new \DateTime('2022-02-02')],
                    ["type" => "bus", "number" => "airport", "departure" => "Barcelona", "arrival" => "Gerona Airport",
                        "departureDate" => new \DateTime('2022-02-03'), "arrivalDate" => new \DateTime('2022-02-03')],
                    ["type" => "train", "number" => "SK455", "departure" => "Gerona Airport", "arrival" => "Stockholm", "seat" => "3A", "gate" => "45B", "bagageDrop" => "344",
                        "departureDate" => new \DateTime('2022-02-04'), "arrivalDate" => new \DateTime('2022-02-04')],
                    ["type" => "train", "number" => "78A", "departure" => "Madrid", "arrival" => "Barcelona", "seat" => "45B",
                        "departureDate" => new \DateTime('2022-02-05'), "arrivalDate" => new \DateTime('2022-02-05')],
                ]
            ],
            [
                "name" => "Voyage valide 2B",
                "steps" => [
                    ["type" => "bus", "number" => "B1", "departure" => "Grasse", "arrival" => "Cannes",
                        "departureDate" => new \DateTime('2022-02-02'), "arrivalDate" => new \DateTime('2022-02-02')],
                    ["type" => "train", "number" => "TER-A", "departure" => "Cannes", "arrival" => "Nice Riquier",
                        "departureDate" => new \DateTime('2022-02-03'), "arrivalDate" => new \DateTime('2022-02-03')],
                    ["type" => "bus", "number" => "B2", "departure" => "Nice Riquier", "arrival" => "Nice",
                        "departureDate" => new \DateTime('2022-02-04'), "arrivalDate" => new \DateTime('2022-02-04')],
                    ["type" => "plane", "number" => "P42", "departure" => "Paris", "arrival" => "Londre", "seat" => "96B", "gate" => "12", "bagageDrop" => "123",
                        "departureDate" => new \DateTime('2022-02-05'), "arrivalDate" => new \DateTime('2022-02-05')],
                    ["type" => "train", "number" => "T9 3/4", "departure" => "Londre", "arrival" => "Hogwarts Castle", "seat" => "6",
                        "departureDate" => new \DateTime('2022-02-06'), "arrivalDate" => new \DateTime('2022-02-06')],
                ]
            ],
            [
                "name" => "Voyage valide 3B",
                "steps" => [
                    ["type" => "bus", "number" => "B1", "departure" => "Grasse", "arrival" => "Cannes",
                        "departureDate" => new \DateTime('2022-02-02'), "arrivalDate" => new \DateTime('2022-02-02')],
                    ["type" => "train", "number" => "TER-A", "departure" => "Cannes", "arrival" => "Nice Riquier",
                        "departureDate" => new \DateTime('2022-02-03'), "arrivalDate" => new \DateTime('2022-02-03')],
                    ["type" => "bus", "number" => "B2", "departure" => "Nice Riquier", "arrival" => "Nice",
                        "departureDate" => new \DateTime('2022-02-04'), "arrivalDate" => new \DateTime('2022-02-04')],
                    ["type" => "plane", "number" => "P42", "departure" => "Paris", "arrival" => "Londre", "seat" => "96B", "gate" => "12", "bagageDrop" => "123",
                        "departureDate" => new \DateTime('2022-02-05'), "arrivalDate" => new \DateTime('2022-02-05')],
                    ["type" => "train", "number" => "T9 3/4", "departure" => "Londre", "arrival" => "Hogwarts Castle", "seat" => "6",
                        "departureDate" => new \DateTime('2022-02-06'), "arrivalDate" => new \DateTime('2022-02-06')],
                ]
            ],
        ];
        foreach ($trips as $tripOriginArray) {
            $trip = new Trip();
            $steps = new ArrayCollection();
            $trip->setName($tripOriginArray["name"]);
            $trip->setCreatedAt(new \DateTime());
            $trip->generateSlug();
            foreach ($tripOriginArray["steps"] as $key => $step) {
                $tripStep = new TripStep();
                $vehicule = Helper::getVehiculeFromArray($step);
                $tripStep->setVehicule($vehicule);
                $tripStep->setTrip($trip);
                $tripStep->setStep($key);
                $tripStep->setDeparture($step["departure"]);
                $tripStep->setArrival($step["arrival"]);
                $tripStep->setDepartureDate($step["departureDate"]);
                $tripStep->setArrivalDate($step["arrivalDate"]);
                $steps->add($tripStep);
            }
            $trip->setSteps($steps);
            $tripProcess = new TripProcess();
            $tripProcess->process($trip);
            if ($tripProcess->isHasError()) {
                dd($tripProcess->getMessages());
            }
            $this->assertSame(false, $tripProcess->isHasError());
        }
    }

    public function testInvalidTrip() {
        $trips = [
            [
                "name" => "Voyage invalide 1",
                "steps" => [
                    ["type" => "plane", "number" => "SK22", "departure" => "Stockholm", "arrival" => "New York JFK", "seat" => "7B", "gate" => "22",
                        "departureDate" => new \DateTime('2022-02-02'), "arrivalDate" => new \DateTime('2022-02-02')],
                    ["type" => "bus", "number" => "airport", "departure" => "Barcelona", "arrival" => "Barcelona",
                        "departureDate" => new \DateTime('2022-02-03'), "arrivalDate" => new \DateTime('2022-02-03')],
                    ["type" => "plane", "number" => "SK455", "departure" => "Gerona Airport", "arrival" => "Gerona Airport", "seat" => "3A", "gate" => "45B", "bagageDrop" => "344",
                        "departureDate" => new \DateTime('2022-02-04'), "arrivalDate" => new \DateTime('2022-02-04')],
                    ["type" => "train", "number" => "78A", "departure" => "Madrid", "arrival" => "Barcelona", "seat" => "45B",
                        "departureDate" => new \DateTime('2022-02-05'), "arrivalDate" => new \DateTime('2022-02-05')],
                ]
            ],
            [
                "name" => "Voyage invalide 2",
                "steps" => [
                    ["type" => "bus", "number" => "B1", "departure" => "Grasse", "arrival" => "Cannes",
                        "departureDate" => new \DateTime('2022-02-02'), "arrivalDate" => new \DateTime('2022-02-02')],
                    ["type" => "train", "number" => "TER-A", "departure" => "Cannes", "arrival" => "Nice Riquier",
                        "departureDate" => new \DateTime('2022-02-03'), "arrivalDate" => new \DateTime('2022-02-03')],
                    ["type" => "bus", "number" => "B2", "departure" => "Nice Riquier", "arrival" => "Nice",
                        "departureDate" => new \DateTime('2022-02-04'), "arrivalDate" => new \DateTime('2022-02-04')],
                    ["type" => "", "number" => "P42", "departure" => "Paris", "arrival" => "Londre", "seat" => "96B", "gate" => "12", "bagageDrop" => "123",
                        "departureDate" => new \DateTime('2022-02-05'), "arrivalDate" => new \DateTime('2022-02-05')],
                    ["type" => "train", "number" => "T9 3/4", "departure" => "Londre", "arrival" => "Hogwarts Castle", "seat" => "6",
                        "departureDate" => new \DateTime('2022-02-06'), "arrivalDate" => new \DateTime('2022-02-06')],
                ]
            ],
            [
                "name" => "Voyage invalide 3",
                "steps" => [
                    ["type" => "bus", "number" => "B1", "departure" => "Grasse", "arrival" => "Cannes",
                        "departureDate" => new \DateTime('2022-02-02'), "arrivalDate" => new \DateTime('2022-02-02')],
                    ["type" => "train", "number" => "TER-A", "departure" => "Cannes", "arrival" => "Nice Riquier",
                        "departureDate" => new \DateTime('2022-02-03'), "arrivalDate" => new \DateTime('2022-02-03')],
                    ["type" => "bus", "number" => "B2", "departure" => "Nice Riquier", "arrival" => "Nice",
                        "departureDate" => new \DateTime('2022-02-04'), "arrivalDate" => new \DateTime('2022-02-04')],
                    ["type" => "plane", "number" => "", "departure" => "", "arrival" => "", "seat" => "", "gate" => "",
                        "departureDate" => new \DateTime('2022-02-05'), "arrivalDate" => new \DateTime('2022-02-05')],
                    ["type" => "plane", "number" => "P42", "departure" => "Paris", "arrival" => "Londre", "seat" => "96B", "gate" => "12", "bagageDrop" => "123",
                        "departureDate" => new \DateTime('2022-02-06'), "arrivalDate" => new \DateTime('2022-02-06')],
                    ["type" => "train", "number" => "T9 3/4", "departure" => "Londre", "arrival" => "Hogwarts Castle", "seat" => "6",
                        "departureDate" => new \DateTime('2022-02-07'), "arrivalDate" => new \DateTime('2022-02-07')],
                ]
            ],
        ];
        foreach ($trips as $tripOriginArray) {
            $trip = new Trip();
            $steps = new ArrayCollection();
            $trip->setName($tripOriginArray["name"]);
            $trip->setCreatedAt(new \DateTime());
            $trip->generateSlug();
            foreach ($tripOriginArray["steps"] as $key => $step) {
                $tripStep = new TripStep();
                $vehicule = Helper::getVehiculeFromArray($step);
                $tripStep->setVehicule($vehicule);
                $tripStep->setTrip($trip);
                $tripStep->setStep($key);
                $tripStep->setDeparture($step["departure"]);
                $tripStep->setArrival($step["arrival"]);
                $tripStep->setDepartureDate($step["departureDate"]);
                $tripStep->setArrivalDate($step["arrivalDate"]);
                $steps->add($tripStep);
            }
            $trip->setSteps($steps);
            $tripProcess = new TripProcess();
            $tripProcess->process($trip);
            $this->assertSame(true, $tripProcess->isHasError());
        }
    }
}
