<?php

namespace App\Tests\Entity;

use App\Entity\Lieu;
use App\Entity\Ville;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LieuTest extends TestCase
{
    public function testGettersSetters(    ): void
    {
        // Creation d'une nouvelle ville
        $nantes = (new Ville())
            ->setNom("Nantes")
            ->setCodePostal(44000);
        // Création d'un nouveau lieu
        $lieu = (new Lieu())
            ->setNom("GameOver")
            ->setLatitude(47.220093)
            ->setLongitude(-1.5495)
            ->setRue("1 Rue Lebrun")
            ->setVille($nantes);
        $this->assertEquals("GameOver", $lieu->getNom());
        $this->assertEquals(47.220093, $lieu->getLatitude());
        $this->assertEquals(-1.5495, $lieu->getLongitude());
        $this->assertEquals("1 Rue Lebrun", $lieu->getRue());
        $this->assertEquals("Nantes", $lieu->getVille()->getNom());
        $this->assertNotNull($lieu->getSorties());
        $this->assertEmpty($lieu->getSorties());
    }
}
