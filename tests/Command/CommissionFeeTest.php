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

//    private CsvParser $csvParser;
//
//    public function setUp(): void
//    {
//        $kernel = self::bootKernel();
//
//        //$mockScvParser = $this->createMock(CsvParser::class);
//        $calculator = $this->getMockBuilder(CommissionFeeSeraCalculator::class)->disableOriginalConstructor()->getMock();
//        $clientFactory = $this->getMockBuilder(ClientFactory::class)->disableOriginalConstructor()->getMock();
//        $mockScvParser = $this->getMockBuilder(CsvParser::class)->disableOriginalConstructor()->getMock();
//
//        $this->csvParser = $mockScvParser;
//        $kernel->getContainer()->set(CsvParser::class, $this->csvParser);
//        $kernel->getContainer()->set(CommissionFeeSeraCalculator::class, $calculator);
//        $kernel->getContainer()->set(ClientFactory::class, $clientFactory);
//
//
//        //$kernel->getContainer()->set(CsvParser::class, $mockScvParser);
//        parent::setUp();
//    }

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
//                    "currency"=> "JPY"
//                ],
//                [
//                    "date"=> "2017-02-19 00:00:00.000000",
//                    "clientId"=> "6",
//                    "clientType"=> "private",
//                    "operationType"=> "withdraw",
//                    "amount"=> "3000000",
//                    "currency"=> "JPY"
//                ]
//            ]
//        );

//        $stub = $this->createMock(CsvParser::class);
//        $stub->method('parseCsv')
//            ->willReturn(['foo'=>'bar']);
        //$this->csvParser->method('parseCsv')->willReturn(['foo'=>'bar']);

        $kernel = self::bootKernel();

        //$mockScvParser = $this->createMock(CsvParser::class);
        $mockCalculator = $this->getMockBuilder(CommissionFeeSeraCalculator::class)->disableOriginalConstructor()->getMock();
        $mockClientFactory = $this->getMockBuilder(ClientFactory::class)->disableOriginalConstructor()->getMock();
        $mockScvParser = $this->getMockBuilder(CsvParser::class)->disableOriginalConstructor()->getMock();

        //$this->csvParser = $mockScvParser;
        $kernel->getContainer()->set(CsvParser::class, $mockScvParser);
        //$kernel->getContainer()->set(CommissionFeeSeraCalculator::class, $mockCalculator);
        //$kernel->getContainer()->set(ClientFactory::class, $mockClientFactory);

        $application = new Application($kernel);
        $application->setAutoExit(false); // may be problem!

        $command = $application->find('app:commission_fee_calculator');

        $commandTester = new CommandTester($command);

        $mockScvParser->expects($this->once())->method('parseCsv')->willReturn([
            [
                "date"=> new \DateTimeImmutable("2014-12-31"),
                "clientId"=> "4",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "1200",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2015-01-01"),
                "clientId"=> "4",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "1000",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-05"),
                "clientId"=> "4",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "1000",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-05"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "deposit",
                "amount"=> "200",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-06"),
                "clientId"=> "2",
                "clientType"=> "business",
                "operationType"=> "withdraw",
                "amount"=> "300",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-06"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-06"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-07"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-07"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-10"),
                "clientId"=> "2",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-10"),
                "clientId"=> "3",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-02-15"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-02-19"),
                "clientId"=> "5",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "3000000",
                "currency"=> "JPY"
            ],

        ]);

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

        $this->assertStringContainsString('0.6/3/0/0.06/1.5/0/0.69/0.34/0.3/3/0/0/8607.4/', $output);

        // ...
    }
}