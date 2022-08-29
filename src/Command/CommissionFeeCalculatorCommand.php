<?php

namespace App\Command;

use App\Interfaces\CommissionFeeCalculatorInterface;
use App\Interfaces\CurrencyConverterInterface;
use App\Services\ClientFactory;
use App\Services\CsvParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:commission_fee_calculator',
    description: 'This command parse csv file with operations like
    deposit or withdraw and calculates commission fee as output based
    on certain  rules',
)]
class CommissionFeeCalculatorCommand extends Command
{
    private CsvParser $csvParser;
    private ClientFactory $clientFactory;
    private CommissionFeeCalculatorInterface $taxSeraCalculator;

    public function __construct(CsvParser $csvParser, ClientFactory $clientFactory, CommissionFeeCalculatorInterface $taxSeraCalculator)
    {
        $this->csvParser = $csvParser;
        $this->clientFactory = $clientFactory;
        $this->taxSeraCalculator = $taxSeraCalculator;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription("Import csv file with operations like deposit or withdraw to calculate commission fee for each one")
            ->addArgument('csvPath', InputArgument::REQUIRED, 'the path to csv file to import')
            ->addArgument('fileName', InputArgument::REQUIRED, 'the name to csv file to import, without the path to it');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvPath = $input->getArgument('csvPath');
        $fileName = $input->getArgument('fileName');
        $csvArray = $this->csvParser->parseCsv($csvPath, $fileName);
        var_dump($csvArray);
        die;

        foreach ($csvArray as $record){
            $this->clientFactory->createClientIfNotExist($record['clientId'], $record['clientType']);
        }

        foreach ($csvArray as $operation){
            $client = $this->clientFactory->findById($operation['clientId']);
            $tax = 0.0;

            if($operation['clientType'] == 'private' && $operation['operationType'] == 'withdraw'){
                $tax = $this->taxSeraCalculator->calculateWithdrawCommissionFeePrivateClient($client, $operation['date'], $operation['amount'], $operation['currency']);
            }
            if($operation['clientType'] == 'business' && $operation['operationType'] == 'withdraw'){
                $tax = $this->taxSeraCalculator->calculateWithdrawCommissionFeeBusinessClient($client, $operation['date'], $operation['amount'], $operation['currency']);
            }
            if($operation['operationType'] == 'deposit'){
                $tax = $this->taxSeraCalculator->calculateDepositCommissionFee($client, $operation['amount']);
            }
            $output->writeln($tax);
        }

        return Command::SUCCESS;
    }
}
