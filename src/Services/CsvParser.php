<?php

namespace App\Services;

use Symfony\Component\Finder\Finder;

class CsvParser
{
    public function parseCsv($csvPath, $fileName): array
    {
       // $csvPath = $input->getArgument('csvPath');
        //$fileName = $input->getArgument('fileName');

        $finder = new Finder();
        $finder->files()
            ->in($csvPath)
            ->name($fileName);

        foreach ($finder as $file)
        {
            $csv = $file;
        }

        $rows = [];
        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                $rows[] = $this->parseRecordToArray($data[0]);
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * @throws \Exception
     */
    private function parseRecordToArray($record): array
    {
//        var_dump($record);
//        die();
        $parsedRecord = [];
        $temp = explode(',', $record);
        $parsedRecord['date'] = new \DateTimeImmutable($temp[0]);
        $parsedRecord['clientId'] = $temp[1];
        $parsedRecord['clientType'] = $temp[2];
        $parsedRecord['operationType'] = $temp[3];
        $parsedRecord['amount'] = $temp[4];
        $parsedRecord['currency'] = $temp[5];


        //var_dump($parsedRecord);
        return $parsedRecord;
    }
}