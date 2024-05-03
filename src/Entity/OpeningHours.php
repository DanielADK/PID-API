<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OpeningHours {
	#[ORM\Id]
	#[ORM\Column(type: "integer")]
	#[ORM\GeneratedValue]
	private int $id;
	#[ORM\Column(type: "integer")]
	private int $from;
	#[ORM\Column(type: "integer")]
	private int $to;
	#[ORM\Column(type: "string")]
	private string $hours;
}