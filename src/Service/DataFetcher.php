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
				if (!isset($openingHour["from"]) || !isset($openingHour["to"]) || !isset($openingHour["hours"])) {
					continue;
				}
				$openingHour = (new OpeningHours())
					->setOpenFrom($openingHour["from"])
					->setOpenTo($openingHour["to"])
					->setHours($openingHour["hours"]);
				$pointOfSale->addOpeningHour($openingHour);
			}

			$pointsOfSale[] = $pointOfSale;
		}

		return $pointsOfSale;
	}
}