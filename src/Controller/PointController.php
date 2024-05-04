<?php

namespace App\Controller;

use App\Repository\OpeningHoursRepository;
use App\Service\DataFetcher;
use App\Service\DataSaver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: "/api/point", name: "point_")]
class PointController extends AbstractController {

	#[Route(path: "/fetch", name: "fetch")]
	public function fetch(DataFetcher $fetcher, DataSaver $saver): JsonResponse {
		$pointsOfSale = $fetcher->fetchData("https://data.pid.cz/pointsOfSale/json/pointsOfSale.json");
		$saver->saveData($pointsOfSale);

		return $this->json(["status" => "success"]);
	}

	#[Route(path: "/open", name: "open")]
	public function getOpenedPoints(Request $request, OpeningHoursRepository $openingHoursRepository, SerializerInterface $serializer): JsonResponse {
		// Get OpeningHoursRepository


		$time = $request->query->get("time", date("H:i"));
		// If time is not set, use current time
		if (!$time) {
			$time = date("H:i");
		}
		// Check if time is in correct format
		if (!preg_match("/^\d{2}:\d{2}$/", $time)) {
			return $this->json(["error" => "Invalid time format"]);
		}
		$date = $request->query->get("date", date("Y-m-d"));
		// If date is not set, use current date
		if (!$date) {
			$date = date("Y-m-d");
		}
		// Check if date is in correct format
		if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
			return $this->json(["error" => "Invalid date format"]);
		}

		// Find points of sale that are open at given time
		$openingHours = $openingHoursRepository->findOpenAt($time, $date);

		// Serialize to custom format
		$customFormat = array();
		foreach($openingHours as $oh) {
			// Serialize PointOfSale
			$customFormat[] = [
				"id" => $oh->getId(),
				"type" => $oh->getType(),
				"name" => $oh->getName(),
				"address" => $oh->getAddress(),
				"latitude" => $oh->getLatitude(),
				"longitude" => $oh->getLongitude(),
				"services" => $oh->getServices(),
				"payMethods" => $oh->getPayMethods(),
				// serialize opening hours
				"openingHours" => $oh->getOpeningHours()->map(function($oh) {
					return [
						"from" => $oh->getDayFrom(),
						"to" => $oh->getDayTo(),
						"hours" => $oh->getTimeFrom()->format("H:i") . "-" . $oh->getTimeTo()->format("H:i"),
					];
				})->toArray()
			];
			// Add link if it is set
			if ($oh->getLink()) {
				$customFormat["link"] = $oh->getLink();
			}
		}
		return $this->json($customFormat);
	}

}