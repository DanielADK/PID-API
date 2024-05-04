<?php

namespace App\Controller;

use App\Service\DataFetcher;
use App\Service\DataSaver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route(path: "/api/point", name: "point_")]
class PointController extends AbstractController {

	#[Route(path: "/fetch", name: "fetch")]
	public function fetch(DataFetcher $fetcher, DataSaver $saver): JsonResponse {
		$pointsOfSale = $fetcher->fetchData("https://data.pid.cz/pointsOfSale/json/pointsOfSale.json");
		$saver->saveData($pointsOfSale);

		return $this->json(["status" => "success"]);
	}
}