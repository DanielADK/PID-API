<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PointOfSale {
	#[ORM\Id]
	#[ORM\Column(type: "integer", nullable: false)]
	private string $id;
	#[ORM\Column(type: "string", nullable: false)]
	private string $type;
	#[ORM\Column(type: "string", nullable: false)]
	private string $name;
	#[ORM\Column(type: "string", nullable: true)]
	private string|null $address;
	#[ORM\ManyToOne(targetEntity: OpeningHours::class, cascade: ["persist"])]
	private OpeningHours $openingHours;
	#[ORM\Column(type: "float", nullable: false)]
	private float $latitude;
	#[ORM\Column(type: "float", nullable: false)]
	private float $longitude;
	#[ORM\Column(type: "integer", nullable: false)]
	private int $services;
	#[ORM\Column(type: "integer", nullable: false)]
	private int $payMethods;
	#[ORM\Column(type: "string", nullable: true)]
	private string $link;
}