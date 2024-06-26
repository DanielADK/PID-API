<?php

namespace App\Repository;

use App\Entity\PointOfSale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PointOfSaleRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, PointOfSale::class);
	}

	public function findAll(): array {
		return $this->createQueryBuilder("pos")
			->getQuery()
			->getResult();
	}
}