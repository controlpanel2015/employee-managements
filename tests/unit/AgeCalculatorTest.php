<?php namespace App\Tests;

use App\Service\AgeCalculator;

class AgeCalculatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \App\Tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $ageCalculator= new AgeCalculator();
        $date_of_birth= new \DateTime('1971-12-16');
        $this->assertEquals(47,$ageCalculator->calculate_age($date_of_birth));

    }
}