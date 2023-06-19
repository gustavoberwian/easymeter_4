<?php

namespace App\Controllers;

use App\Models\Mapa_model;

class Mapa extends UNO_Controller {

    private Mapa_model $mapa_model;

    public function __construct()
    {
        parent::__construct();

        $this->mapa_model = new Mapa_model();
    }

    public function index()
    {
        $data = array();

        echo $this->render('index', $data, false);
    }

    public function get_buildings()
    {
        $buildings = $this->mapa_model->get_buildings();

        $coordenadas = $this->calculaCirculo();

        $dados = [
            array(
                "type" => "FeatureCollection",
                "features" => [
                    array(
                        "id" => "w137353386",
                        "properties" => array(
                            "height" => 25,
                            "altitude" => 20,
                            "material" => "brick",
                            "color" => "#fff",
                            "colorHover" => "#ededed",
                            "roofMaterial" => "tar_paper",
                            "roofShape" => "pyramid",
                            "topColor" => "#fff",
                            "opacity" => 0.7
                        ),
                        "geometry" => array(
                            "type" => "Polygon",
                            "coordinates" => [$coordenadas]
                        ),
                        "type" => "Feature"
                    )
                ]
            ),
            array(
                "type" => "FeatureCollection",
                "features" => [
                    array(
                        "id" => "w137353387",
                        "properties" => array(
                            "height" => 20,
                            "altitude" => 0,
                            "material" => "brick",
                            "color" => "#53acd6",
                            "colorHover" => "#AAD3DF",
                            "roofMaterial" => "tar_paper",
                            "roofShape" => "pyramid",
                            "topColor" => "#53acd6",
                            "opacity" => 1
                        ),
                        "geometry" => array(
                            "type" => "Polygon",
                            "coordinates" => [$coordenadas]
                        ),
                        "type" => "Feature"
                    )
                ]
            )
        ];

        echo json_encode($dados);
    }

    public function generateCircleCoordinates($center, $radius, $segments) {
        $coordinates = array();
        $x1 = deg2rad($center[0]);
        $y1 = deg2rad($center[1]);
        $earthRadius = 6371000;
        $d = $radius / $earthRadius;

        for ($i = 0; $i < $segments; $i++) {
            $angle = deg2rad(360 / $segments * $i);
            $x = $center[0] + rad2deg($d / cos(deg2rad($center[1]))) * cos($angle);
            $y = $center[1] + rad2deg($d) * sin($angle);
            $coordinates[] = [$x, $y];
    }
        return $coordinates;
    }

    public function calculaCirculo()
    {
        // Exemplo de uso:
        $center = array(-51.146157378351745, -29.670586757512122);
        $radius = 10;
        $segments = 360;

        $circleCoordinates = $this->generateCircleCoordinates($center, $radius, $segments);

        $response = array();

        foreach ($circleCoordinates as $ponto) {
            $response[] = [$ponto[0], $ponto[1]];
        }

        return $response;

        foreach ($circleCoordinates as $ponto) {
            echo "[{$ponto[1]}, {$ponto[0]}],\n";
        }

        return;

        // Coordenadas centrais
        $x_centro = 40.545368;
        $y_centro = -74.243698;

        // Coordenadas da borda do círculo
        $x_borda = 40.545369;
        $y_borda = -74.243883;

        // Calcula o raio
        $raio = sqrt(pow($x_borda - $x_centro, 2) + pow($y_borda - $y_centro, 2));

        // Número de pontos desejado
        $num_pontos = 46;

        // Calcula os pontos do círculo com o número desejado de coordenadas
        $pontos_do_circulo = $this->calcularPontosDoCirculo($x_centro, $y_centro, $raio, $num_pontos);

        $response = array();

        foreach ($pontos_do_circulo as $ponto) {
            $response[] = [$ponto[1], $ponto[0]];
        }

        return $response;
    }
}