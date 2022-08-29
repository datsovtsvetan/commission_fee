<?php

namespace App\Tests\Command;

use App\Services\CsvParser;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CommissionFeeTest extends KernelTestCase
{

    private CsvParser $csvParser;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $mockScvParser = $this->createMock(CsvParser::class);

        //$mockScvParser = $this->getMockBuilder(CsvParser::class)->disableOriginalConstructor()->getMock();
        $this->csvParser = $mockScvParser;
        static::$kernel->getContainer()->set(CsvParser::class, $mockScvParser);
        $mockScvParser->expects($this->once())->method('parseCsv')->willReturn(  [
            [
            "date"=> "2016-02-19 00:00:00.000000",
            "clientId"=> "5",
            "clientType"=> "private",
            "operationType"=> "withdraw",
            "amount"=> "3000000",
            "currency"=> "JPY"]
            ]
        );

        //$kernel->getContainer()->set(CsvParser::class, $mockScvParser);
        parent::setUp();
    }

    public function testCommissionFeeCommand()
    {
        //$this->assertTrue(false);
        //$this->assertTrue(true);

        //$mockScvParser = $this->createMock(CsvParser::class);

        $kernel = self::bootKernel();

        $application = new Application($kernel);
        $application->setAutoExit(false); // may be problem!

        $command = $application->find('app:commission_fee_calculator');

        $commandTester = new CommandTester($command);

        //$commandTester->setInputs()
//        $bla = $commandTester->getInput();
//        var_dump($bla);
        $commandTester->execute([
            // pass arguments to the helper
            'csvPath' => 'C:\Users\datso\commission_fee\public\\',
            'fileName' => 'input.scv'

            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        //var_dump($output);
        $this->assertStringContainsString('Username: Wouter', $output);

        // ...
    }
}