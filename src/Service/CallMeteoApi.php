<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use DateTimeZone;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallMeteoApi
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getData($currentWeather = true, $latitude = null, $longitude = null): array
    {
        $url = "";
        if ($currentWeather) {
            $url = "https://api.open-meteo.com/v1/forecast?latitude=".$latitude."&longitude=".$longitude."&current_weather=true";
        } else {
            $dt = new DateTime("now", new DateTimeZone('Europe/Paris'));
            $interval = new \DateInterval('P7D');
            $url = "https://api.open-meteo.com/v1/forecast?latitude=".$latitude."&longitude=".$longitude."&daily=weathercode,temperature_2m_min,temperature_2m_max&timezone=auto&start_date=".$dt->format('Y-m-d')."&end_date=".$dt->add($interval)->format('Y-m-d');;
        }
        $response = $this->client->request('GET',
            $url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
        ]);

        $respArray = $response->toArray();
        $res = [];
        if ($currentWeather) {
            $res = array_map(null,
                $respArray["current_weather"],
                $this->getCodeToImage($respArray["current_weather"]["weathercode"])
                );
        } else {
            $res = array_map(null,
                $respArray["daily"]["time"],
                array_map(null, $respArray["daily"]["weathercode"],$this->getCodeArrayToImage($respArray)),
                $respArray["daily"]["temperature_2m_max"],
                $respArray["daily"]["temperature_2m_min"]);
        }
        dump($res);
        return $res;
    }

    public function getCodeArrayToImage($data): array
    {
        $weather = [];
        foreach ($data["daily"]["weathercode"] as $m) {
            $weather[] = $this->getCodeToImage($m);
        }
        return $weather;
    }

    public function getCodeToImage($code): array
    {
        $weather = "";
        switch ($code) {
            case 0:
                $weather = "<span class='material-symbols-outlined'>clear_day</span> <div class='desc'>Ciel dégagé</div>";
                break;
            case 1:
                $weather = "<span class='material-symbols-outlined'>clear_day</span> <div class='desc'>Principalement clair</div>";
                break;
            case 2:
                $weather = "<span class='material-symbols-outlined'>cloudy</span> <div class='desc'>Partiellement nuageux</div>";
                break;
            case 3:
                $weather = "<span class='material-symbols-outlined'>cloudy</span> <div class='desc'>Ciel couvert</div>";
                break;
            case 45:
                $weather = "<span class='material-symbols-outlined'>foggy</span> <div class='desc'>Brouillard</div>";
                break;
            case 48:
                $weather = "<span class='material-symbols-outlined'>foggy</span> <div class='desc'>Dépôt de brouillard givré</div>";
                break;
            case 51:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Bruine légère</div>";
                break;
            case 53:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Bruine modérée</div>";
                break;
            case 55:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Bruine dense</div>";
                break;
            case 56:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Bruine verglaçante légère</div>";
                break;
            case 57:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Bruine verglaçante dense</div>";
                break;
            case 61:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Pluie légère</div>";
                break;
            case 63:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Pluie modérée</div>";
                break;
            case 65:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Forte Pluie</div>";
                break;
            case 66:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Légère pluie verglaçante</div>";
                break;
            case 67:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Pluie verglaçante forte ou modérée</div>";
                break;
            case 71:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Légère chutes de neige</div>";
                break;
            case 73:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Chutes de neige modérée</div>";
                break;
            case 75:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Forte chutes de neige</div>";
                break;
            case 77:
                $weather = "<span class='material-symbols-outlined'>snowing</span> <div class='desc'>Grains de neige</div>";
                break;
            case 80:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Légère averses de pluie</div>";
                break;
            case 81:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Averses de pluie modérée</div>";
                break;
            case 82:
                $weather = "<span class='material-symbols-outlined'>rainy</span> <div class='desc'>Violentes averses de pluie</div>";
                break;
            case 85:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Légère averses de neige</div>";
                break;
            case 86:
                $weather = "<span class='material-symbols-outlined'>cloudy_snowing</span> <div class='desc'>Averses de neige modérées ou fortes</div>";
                break;
            case 95:
                $weather = "<span class='material-symbols-outlined'>thunderstorm</span> <div class='desc'>Leger ou fort orage sans grêle</div>";
                break;
            case 96:
                $weather = "<span class='material-symbols-outlined'>thunderstorm</span> <div class='desc'>Leger ou modérée orage avec grêle</div>";
                break;
            case 99:
                $weather = "<span class='material-symbols-outlined'>thunderstorm</span> <div class='desc'>Fort orage avec grêle</div>";
                break;
            default:
                $weather = null;
        }
        return [$weather];
    }
}