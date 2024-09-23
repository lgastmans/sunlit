<?php

namespace App\Console\Commands;

use App\Models\Dealer;
use App\Models\State;
use Illuminate\Console\Command;

class importDealers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:dealers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the dealers from CSV file';

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
     *
     * @return int
     */
    public function handle(): int
    {
        /*
            File columns in order in CSV | corresponding column name:

                customer names  | company not null
                location        | city not null
                state           | state_id can be null
                contact name    | contact_person can be null
                contact number  | phone not null
                email           | email can be null
                gstin           | gstin can be null
                address         | address not null
                address2        | address2 can be null
                pin             | can be null
        */

        $file = public_path('dealers-import.csv');
        $dataArr = $this->csvToArray($file);

        for ($i = 0; $i < count($dataArr); $i++) {

            $state = State::where('name', 'like', '%'.$dataArr[$i]['state_id'].'%')->first();

            if ($state) {
                $dataArr[$i]['state_id'] = $state->id;
            } else {
                $dataArr[$i]['state_id'] = 1;
            }

            $dealer = Dealer::insert($dataArr[$i]);
        }

        return 'Import complete';
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

                    $data[$i]['email'] = trim($row[6]);

                    $data[$i]['gstin'] = $row[7];

                    if ((empty($row[8])) || (is_null($row[8]))) {
                        $data[$i]['address'] = $row[8];
                    } else {
                        $data[$i]['address'] = $row[8];
                    }

                    if ((empty($row[9])) || (is_null($row[9]))) {
                        $data[$i]['address2'] = '';
                    } else {
                        $data[$i]['address2'] = $row[9];
                    }

                    if ((empty($row[10])) || (is_null($row[10]))) {
                        $data[$i]['zip_code'] = ' ';
                    } else {
                        $data[$i]['zip_code'] = $row[10];
                    }

                    $i++;
                }
            }
            fclose($handle);
        }

        return $data;
    }
}
