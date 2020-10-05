<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Lib\Position;
use App\Lib\Triangulation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class LocalizationController
 * @package App\Http\Controllers
 */
class LocationController extends Controller
{
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $config;

    /**
     * LocalizationController constructor.
     */
    public function __construct()
    {
        $this->config = config("satelliteInformation");
    }

    /**
     * @param LocationRequest $request
     * @return JsonResponse
     */
    public function topSecret(LocationRequest $request) {
        $messages = [];
        $distances = [];
        $inputs = $request->input("satelites");
        foreach ($inputs as $distance) {
            if (in_array($distance['name'], array_keys($this->config))) {
                $distances[] = $distance['distance'];
                $messages[] = $distance["message"];
            }
        }

        $coordinates = $this->getLocation($distances);

        return response()->json([
            "position" => [
                "X" => $coordinates->latitude(),
                "Y" => $coordinates->longitude()
            ],
            "message" => $this->getMessage($messages)
        ]);
    }

    /**
     * @param Request $request
     * @param string $satellite_name
     * @return JsonResponse
     */
    public function topSecretSplit(Request $request, string $satellite_name)
    {
        abort_unless(
            in_array($satellite_name, array_keys($this->config)),
            JsonResponse::HTTP_NOT_FOUND,
            "The satellite [{$satellite_name}] does not exist."
        );

        return response()->json([
            "position" => [
                "X" => $this->config[$satellite_name][0],
                "Y" => $this->config[$satellite_name][1]
            ],
            "message" => $this->getMessage([$request->input('message')])
        ]);
    }

    /**
     * @param array $distances
     * @return \App\Lib\Point|false
     * @throws \Exception
     */
    private function getLocation(array $distances)
    {
        $coordenates = [];
        foreach ($distances as $distance) {
            $coordenates[] = $distance;
        }

        $kenobi = new Position($this->config['kenobi'][0], $this->config['kenobi'][1], $coordenates[0]);
        $skywalker = new Position($this->config['skywalker'][0], $this->config['skywalker'][1], $coordenates[1]);
        $sato = new Position($this->config['sato'][0], $this->config['sato'][1], $coordenates[2]);

        $triangulation = new Triangulation($kenobi, $skywalker, $sato);
        return $triangulation->position();
    }

    /**
     * @param array $messages
     * @return string
     */
    private function getMessage(array $messages)
    {
        $buildMessage = [];
        foreach ($messages as $message) {
            foreach ($message as $key => $value) {
                if (!empty($value)) {
                    $buildMessage[$key] = $value;
                }
            }
        }
        abort_unless(
            (count($messages[0]) === count($buildMessage)),
            JsonResponse::HTTP_NOT_FOUND,
            "The message did not arrive complete."
        );
        ksort($buildMessage);
        return implode(" ", $buildMessage);
    }
}
