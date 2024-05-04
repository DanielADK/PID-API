<?php

namespace App\Service;

use App\Entity\OpeningHours;
use App\Entity\PointOfSale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DataSaver {
	private EntityManagerInterface $em;
	/** @var array<string, OpeningHours> */
	private array $openingHoursCache;

	public function __construct(EntityManagerInterface $em) {
		$this->em = $em;
		$this->openingHoursCache = array();
	}

	/**
	 * @description Save data to database
	 * @param array<PointOfSale> $data
	 *
	 * @return void
	 */
	public function saveData(array $data): void {
		foreach ($data as $point) {
			if ($point instanceof PointOfSale) {
				// Check if point already exists
				$existingPoint = $this->em->getRepository(PointOfSale::class)->findOneBy([
					'id' => $point->getId(),
				]);
				if ($existingPoint) {
					// update changes to existing and set $point to existing one
					$existingPoint->setType($point->getType());
					$existingPoint->setName($point->getName());
					$existingPoint->setAddress($point->getAddress());
					$existingPoint->setLatitude($point->getLatitude());
					$existingPoint->setLongitude($point->getLongitude());
					$existingPoint->setServices($point->getServices());
					$existingPoint->setPayMethods($point->getPayMethods());

					$point = $existingPoint;
				}
				foreach ($point->getOpeningHours() as $openingHour) {
					if (!$openingHour instanceof OpeningHours) {
						continue;
					}
					$key = $openingHour->getKey();
					if (!isset($this->openingHoursCache[$key])){
						$found = $this->em->getRepository(OpeningHours::class)->findOneBy([
							'open_from' => $openingHour->getOpenFrom(),
							'open_to' => $openingHour->getOpenTo(),
							'hours' => $openingHour->getHours(),
						]);
						if ($found) {
							$this->openingHoursCache[$key] = $found;
						}
					}
					$existingOpeningHour = $this->openingHoursCache[$key];
					$this->em->persist($existingOpeningHour);
					$this->openingHoursCache[$key] = $existingOpeningHour;
				}

				$this->em->persist($point);
				$this->em->flush();
			}
		}
		$this->em->flush();
	}
}