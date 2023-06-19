<?php


namespace App\Controllers;

use App\Models\Mapa_model;

class Mapa extends UNO_Controller
{

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

        $dados = array();
        foreach ($buildings as $b) {

            $coordenadas = $this->calculaCirculo(array($b->latitude, $b->longitude), $b->diametro);

            $dados[] = array(
                "type" => "FeatureCollection",
                "features" => [
                    array(
                        "id" => "w137353386",
                        "properties" => array(
                            "height" => round($b->altura - $b->nivel, 0),
                            "totalHeight" => round($b->altura, 0),
                            "altitude" => round($b->nivel, 0),
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
            );

            $dados[] = array(
                "type" => "FeatureCollection",
                "features" => [
                    array(
                        "id" => "w137353387",
                        "properties" => array(
                            "height" => round($b->nivel, 0),
                            "totalHeight" => round($b->nivel, 0),
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
            );
        }

        echo json_encode($dados);
    }

    public function generateCircleCoordinates($center, $radius, $segments)
    {
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

    public function calculaCirculo($coord, $diametro)
    {
        // Exemplo de uso:
        $center = $coord;
        $radius = $diametro;
        $segments = 360;

        $circleCoordinates = $this->generateCircleCoordinates($center, $radius, $segments);

        $response = array();

        foreach ($circleCoordinates as $ponto) {
            $response[] = [$ponto[0], $ponto[1]];
        }

        return $response;
    }
}