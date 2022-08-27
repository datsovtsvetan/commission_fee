<?php

namespace App\Command;

use App\Services\ClientFactory;
use App\Services\CsvParser;
use App\Services\CurrencyConverter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:commission_fee_calculator',
    description: 'This command parse csv file with operations like
    deposit or withdraw and calculates commission fee as output based
    on certain  rules',
)]
class CommissionFeeCalculatorCommand extends Command
{

    private CsvParser $csvParser;
    private CurrencyConverter $currencyConverter;
    private ClientFactory $clientFactory;

    public function __construct(CsvParser $csvParser, CurrencyConverter $currencyConverter, ClientFactory $clientFactory)
    {
        $this->currencyConverter = $currencyConverter;
        $this->csvParser = $csvParser;
        $this->clientFactory = $clientFactory;
        parent::__construct();

    }

    protected function configure(): void
    {
        $this->setDescription("Import csv file with operations like deposit or withdraw to calculate commission fee for each one")
            ->addArgument('csvPath', InputArgument::REQUIRED, 'the path to csv file to import')
            ->addArgument('fileName', InputArgument::REQUIRED, 'the name to csv file to import, without the path to it')

        ;
    }

//    private array $csvParsingOptions = array(
//        'finder_in' => 'app/Resources/',
//        'finder_name' => 'countries.csv',
//        'ignoreFirstLine' => true
//    );

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvPath = $input->getArgument('csvPath');
        $fileName = $input->getArgument('fileName');
        $csvArray = $this->csvParser->parseCsv($csvPath, $fileName);

        foreach ($csvArray as $record){
            $this->clientFactory->createClientIfNotExist($record['clientId'], $record['clientType']);
        }

//        $testAmountInEuro = $this->currencyConverter->convertToEuro(3.92, "BGN");
//        $testClient = $this->clientFactory->findById($csvArray[0]['clientId']);
//
//        $testClient->calculateWithdrawCommissionFee(new \DateTimeImmutable('2015-01-04'), $testAmountInEuro);
//
//        var_dump($testClient->testOnlyGetHistoryWithdraws());
//        die();


//        var_dump($this->clientFactory->getClients());
//        die;

//        var_dump($this->currencyConverter->convert(1, 'AZN', 'BGN'));
//        die();


//        foreach ($csvArray as $record){
//            $output->writeln($record);
//        }

        return Command::SUCCESS;
    }


    /**
     * Parse a csv file
     *
     * @param $input
     * @param $output
     * @return array
     */
//    private function parseCsv($input, $output): array
//    {
//        //$ignoreFirstLine = $this->csvParsingOptions['ignoreFirstLine'];
//
//
//        //$io = new SymfonyStyle($input, $output);
//        $csvPath = $input->getArgument('csvPath');
//        $fileName = $input->getArgument('fileName');
//
//        $finder = new Finder();
//        $finder->files()
//            ->in($csvPath)
//            ->name($fileName);
//
//        foreach ($finder as $file)
//        {
//            $csv = $file;
//        }
//
//        $rows = array();
//        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
//            $i = 0;
//            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
//                $i++;
//                //if ($ignoreFirstLine && $i == 1) { continue; }
//                $rows[] = $data;
//            }
//            fclose($handle);
//        }
//


//        return $rows;
//    }
}
