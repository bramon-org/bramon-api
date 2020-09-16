<?php

namespace App\Console\Commands;

use App\Models\Station;
use App\Models\User;
use Illuminate\Console\Command;
use League\Csv\Exception as CsvException;
use League\Csv\Reader;

class ImportOperatorsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "import:local:operators {file}";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Import operators and stations from a local file.";

    /**
     * Execute the console command.
     *
     * @return void
     * @throws CsvException
     */
    public function handle(): void
    {
        $inputFile = $this->argument('file');

        $csv = Reader::createFromPath($inputFile, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $record) {
            $user = new User;
            $user->name = "{$record['first_name']} {$record['last_name']}";
            $user->password = (new User)->generatePassword();
            $user->api_token = (new User)->generateApiToken();
            $user->email = $record['user_email'];
            $user->created_at = $record['user_registered'];
            $user->save();

            for ($cameraIndex = 1; $cameraIndex <= 10; $cameraIndex++) {
                if (empty($record['camera_estacao_' . $cameraIndex])
                    && empty($record['placa_de_captura_estacao_' . $cameraIndex])
                    && empty($record['lente_estacao_' . $cameraIndex])
                    && empty($record['nome_da_estacao_' . $cameraIndex])
                ) {
                    continue;
                }

                if (preg_match('/^field_/i', $record['nome_da_estacao_' . $cameraIndex])) {
                    continue;
                }

                $station = new Station;
                $station->visible = $record['mostrar_na_lista_estacao_' . $cameraIndex] === 'Sim';
                $station->name = $record['nome_da_estacao_' . $cameraIndex];
                $station->latitude = $record['latitude_estacao_' . $cameraIndex];
                $station->longitude = $record['longitude_estacao_' . $cameraIndex];
                $station->elevation = $record['elevacao_estacao_' . $cameraIndex];
                $station->azimuth = $record['azimute_estacao_' . $cameraIndex];
                $station->camera_model = $record['camera_estacao_' . $cameraIndex];
                $station->camera_lens = $record['lente_estacao_' . $cameraIndex];
                $station->camera_capture = $record['placa_de_captura_estacao_' . $cameraIndex];
                $station->user_id = $user->id;
                $station->fov = $record['estacoes_0_cameras_' . $cameraIndex . '__field_of_view'];
                $station->city = $record['estacoes_0_uf'];
                $station->state = $record['estacoes_0_uf'];
                $station->country = 'BR';
                $station->save();

                $this->info("{$user->name} - {$station->name}");
            }
        }

        $this->info('Done.');
    }
}
