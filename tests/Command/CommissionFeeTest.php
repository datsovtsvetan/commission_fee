<?php

namespace App\Tests\Command;

use App\Interfaces\CommissionFeeCalculatorInterface;
use App\Services\ClientFactory;
use App\Services\CommissionFeeSeraCalculator;
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

        //$mockScvParser = $this->createMock(CsvParser::class);
        $calculator = $this->getMockBuilder(CommissionFeeSeraCalculator::class)->disableOriginalConstructor()->getMock();
        $clientFactory = $this->getMockBuilder(ClientFactory::class)->disableOriginalConstructor()->getMock();
        $mockScvParser = $this->getMockBuilder(CsvParser::class)->disableOriginalConstructor()->getMock();

        $this->csvParser = $mockScvParser;
        $kernel->getContainer()->set(CsvParser::class, $this->csvParser);
        $kernel->getContainer()->set(CommissionFeeSeraCalculator::class, $calculator);
        $kernel->getContainer()->set(ClientFactory::class, $clientFactory);


        //$kernel->getContainer()->set(CsvParser::class, $mockScvParser);
        parent::setUp();
    }

    public function testCommissionFeeCommand()
    {
        //$this->assertTrue(false);
        //$this->assertTrue(true);

        //$mockScvParser = $this->createMock(CsvParser::class);
//        $this->csvParser->expects($this->once())->method('parseCsv')->willReturn(  [
//                [
//                    "date"=> "2016-02-19 00:00:00.000000",
//                    "clientId"=> "5",
//                    "clientType"=> "private",
//                    "operationType"=> "withdraw",
//                    "amount"=> "3000000",
//                    "currency"=> "JPY"]
//            ]
//        );
        $this->csvParser->expects($this->once())->method('parseCsv')->willReturn('bla');

        $kernel = self::bootKernel();

        $application = new Application($kernel);
        $application->setAutoExit(false); // may be problem!

        $command = $application->find('app:commission_fee_calculator');

        $commandTester = new CommandTester($command);

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

        $this->assertStringContainsString('Username: Wouter', $output);

        // ...
    }
}