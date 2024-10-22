<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the initial inventory from CSV file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /*
            still to be coded as a command (code below is from Dealers command)
            right now the import command is in the ProductController
            and run through the route /products/import
        */
        return 0;
    }

    /*
        convert the csv file to array
    */
    private static function csvToArray($filename = '', $delimiter = "\t")
    {
        if (! file_exists($filename) || ! is_readable($filename)) {
            return 'File not found '.$filename;
        }

        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            $i = 0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {

                if (strpos($row[0], 'Sl.No') !== false) {
                    continue;
                } else {

                    if ((empty($row[1])) || (is_null($row[1]))) {
                        $data[$i]['company'] = 'company '.$i;
                    } else {
                        $data[$i]['company'] = $row[1];
                    }

                    if ((empty($row[2])) || (is_null($row[2]))) {
                        $data[$i]['city'] = 'city '.$i;
                    } else {
                        $data[$i]['city'] = $row[2];
                    }

                    // load the string, the id will be set in the import function
                    if ((empty($row[3])) || (is_null($row[3]))) {
                        $data[$i]['state_id'] = 1;
                    } else {
                        $data[$i]['state_id'] = trim($row[3]);
                    }

                    $data[$i]['contact_person'] = $row[4];

                    if ((empty($row[5])) || (is_null($row[5]))) {
                        $data[$i]['phone'] = 'phone '.$i;
                    } else {
                        $data[$i]['phone'] = $row[5];
                    }

                    $data[$i]['email'] = $row[6];

                    $data[$i]['gstin'] = $row[7];

                    if ((empty($row[8])) || (is_null($row[8]))) {
                        $data[$i]['address'] = $row[8];
                    } else {
                        $data[$i]['address'] = $row[8];
                    }

                    // as the following columns are not given in the csv file and are required, set default values
                    $data[$i]['zip_code'] = ' ';

                    $i++;
                }
            }
            fclose($handle);
        }

        return $data;
    }
}
