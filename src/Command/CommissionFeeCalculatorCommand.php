<?php

namespace App\Command;

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
    description: 'Add a short description for your command',
)]
class CommissionFeeCalculatorCommand extends Command
{

    private CsvParser $csvParser;
    private CurrencyConverter $currencyConverter;

    public function __construct(CsvParser $csvParser, CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
        $this->csvParser = $csvParser;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription("Import csv file with fiscal operations like deposit or withdraw to calculate commission fee from each one")
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
        // use the parseCSV() function
        $csvArray = $this->csvParser->parseCsv($input);

//        var_dump($csvArray);
//        die;

//        var_dump($this->currencyConverter->convert(1, 'AZN', 'BGN'));
//        die();


        foreach ($csvArray as $record){
            $output->writeln($record);
        }
       // return $csv; // I added this

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
