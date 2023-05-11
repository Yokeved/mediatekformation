<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Formation;

class FormationTest extends TestCase {

    public function testGetPublishedAtString() {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime("2023-05-04"));
        $this->assertEquals("04/05/2023", $formation->getPublishedAtString());
        
    }

}