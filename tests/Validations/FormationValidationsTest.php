<?php

namespace App\Tests\Validations;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Formation;

class FormationValidationsTest extends KernelTestCase
{
    public function getFormation(): Formation
    {
        return (new Formation())
            ->setPublishedAt(new \DateTime('24-10-2022'));
    }

    public function testValidPublishedAtFormation()
    {
        $formation = $this->getFormation()->setPublishedAt(new \DateTime('2022-10-24'));
        $this->assertErrors($formation, 0);
    }

    public function assertErrors(Formation $formation, int $nbErreursAttendues)
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $errors = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $errors);
    }
}
