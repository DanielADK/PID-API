<?php

namespace App\Service;

use App\Entity\OpeningHours;
use App\Entity\PointOfSale;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DataFetcher {
	private HttpClientInterface $client;

	public function __construct(HttpClientInterface $client) {
		$this->client = $client;
	}

	/**
	 * @description Fetch data from endpoint
	 *
	 * @param string $endpoint
	 *
	 * @return array<PointOfSale>
	 * @throws \Exception
	 */
	public function fetchData(string $endpoint): array {
		try {
			$response = $this->client->request(
				"GET",
				$endpoint
			);
			$data = json_decode($response->getContent(), true);
		} catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
			error_log($e->getMessage());
			return [];
		}

		$pointsOfSale = array();
		foreach($data as $item) {
			// Check if all required fields are present
			if (!is_array($item) ||
				!isset($item["id"]) ||
				!isset($item["type"]) ||
				!isset($item["name"]) ||
				!isset($item["lat"]) ||
				!isset($item["lon"]) ||
				!isset($item["services"]) ||
				!isset($item["payMethods"]) ||
				!isset($item["openingHours"])) {
				continue;
			}
			// Point of sale
			$pointOfSale = (new PointOfSale())
				->setId($item["id"])
				->setType($item["type"])
				->setName($item["name"])
				->setAddress($item["address"] ?? null)
				->setLatitude($item["lat"])
				->setLongitude($item["lon"])
				->setServices($item["services"])
				->setPayMethods($item["payMethods"]);

			foreach ($item["openingHours"] as $openingHour) {
				// Check if all required fields are present
				if (!is_array($openingHour) ||
					!is_numeric($openingHour["from"]) ||
					!is_numeric($openingHour["to"]) ||
					!isset($openingHour["hours"])) {
					continue;
				}
				// from and to set to integer
				$openingHour["from"] = (int) $openingHour["from"];
				$openingHour["to"] = (int) $openingHour["to"];

				// Parse hours to single ranges in hours
				$timeRanges = explode(",", $openingHour["hours"]);
				foreach ($timeRanges as $timeRange) {
					// Get first and last hour
					$fromto = explode("-", $timeRange);
					if (count($fromto) !== 2) {
						continue;
					}
					// Create opening hour object
					$openingHourObject = (new OpeningHours())
						->setDayFrom($openingHour["from"])
						->setDayTo($openingHour["to"])
						->setTimeFrom(new \DateTime($fromto[0]))
						->setTimeTo(new \DateTime($fromto[1]));
					// Add opening hour to point of sale
					$pointOfSale->addOpeningHour($openingHourObject);
				}
			}

			$pointsOfSale[] = $pointOfSale;
		}

		return $pointsOfSale;
	}
}