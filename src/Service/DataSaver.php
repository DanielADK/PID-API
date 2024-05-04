<?php

namespace App\Service;

use App\Entity\OpeningHours;
use App\Entity\PointOfSale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DataSaver {
	private EntityManagerInterface $em;
	private array $openingHoursCache;

	public function __construct(EntityManagerInterface $em, CacheInterface $cache) {
		$this->em = $em;
		$this->openingHoursCache = array();
	}

	/**
	 * @param array<PointOfSale> $data
	 *
	 * @return void
	 */
	public function saveData(array $data): void {
		foreach ($data as $point) {
			if ($point instanceof PointOfSale) {
				$existingPoint = $this->em->getRepository(PointOfSale::class)->findOneBy([
					'id' => $point->getId(),
				]);
				if ($existingPoint) {
					// Write changes to existing and set $point to existing one
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
					$key = $openingHour->getOpenFrom() . $openingHour->getOpenTo() . $openingHour->getHours();
					if (!isset($this->openingHoursCache[$key])) {
						$this->openingHoursCache[$key] = $this->em->getRepository(OpeningHours::class)->findOneBy([
							'open_from' => $openingHour->getOpenFrom(),
							'open_to' => $openingHour->getOpenTo(),
							'hours' => $openingHour->getHours(),
						]);
					}
					$existingOpeningHour = $this->openingHoursCache[$key];

					if ($existingOpeningHour) {
						$point->removeOpeningHour($openingHour);
						$point->addOpeningHour($existingOpeningHour);
					} else {
						$this->em->persist($openingHour);
						$this->openingHoursCache[$key] = $openingHour;
					}
				}

				$this->em->persist($point);
				$this->em->flush();
			}
		}
		$this->em->flush();
	}
}