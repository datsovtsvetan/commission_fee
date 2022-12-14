<?php

namespace App\Services;

use Symfony\Component\Finder\Finder;

class CsvParser
{
    private Finder $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function parseCsv(string $csvPath, string $fileName): array
    {
        $this->finder->files()
            ->in($csvPath)
            ->name($fileName);

        foreach ($this->finder as $file)
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
        $parsedRecord = [];
        $toArray = explode(',', $record);
        $parsedRecord['date'] = new \DateTimeImmutable($toArray[0]);
        $parsedRecord['clientId'] = $toArray[1];
        $parsedRecord['clientType'] = $toArray[2];
        $parsedRecord['operationType'] = $toArray[3];
        $parsedRecord['amount'] = $toArray[4];
        $parsedRecord['currency'] = $toArray[5];

        return $parsedRecord;
    }
}