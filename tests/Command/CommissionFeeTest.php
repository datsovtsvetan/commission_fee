<?php

namespace App\Tests\Command;

use App\Services\CsvParser;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CommissionFeeTest extends KernelTestCase
{

    public function testCommissionFeeCommand()
    {
        $kernel = self::bootKernel();

        $mockScvParser = $this->getMockBuilder(CsvParser::class)->disableOriginalConstructor()->getMock();


        $kernel->getContainer()->set('test.'.CsvParser::class, $mockScvParser);

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
                "amount"=> "1200.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2015-01-01"),
                "clientId"=> "4",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "1000.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-05"),
                "clientId"=> "4",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "1000.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-05"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "deposit",
                "amount"=> "200.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-06"),
                "clientId"=> "2",
                "clientType"=> "business",
                "operationType"=> "withdraw",
                "amount"=> "300.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-06"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "30000",
                "currency"=> "JPY"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-06"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "1000.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-07"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "100.00",
                "currency"=> "USD"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-07"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "100.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-10"),
                "clientId"=> "2",
                "clientType"=> "business",
                "operationType"=> "deposit",
                "amount"=> "10000.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-01-10"),
                "clientId"=> "3",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "1000.00",
                "currency"=> "EUR"
            ],
            [
                "date"=> new \DateTimeImmutable("2016-02-15"),
                "clientId"=> "1",
                "clientType"=> "private",
                "operationType"=> "withdraw",
                "amount"=> "300.00",
                "currency"=> "EUR"
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
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertSame("0.6\r\n3\r\n0\r\n0.06\r\n1.5\r\n0\r\n0.69\r\n0.34\r\n0.3\r\n3\r\n0\r\n0\r\n8607.4\r\n", $output);
    }
}