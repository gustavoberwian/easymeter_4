<?php

/**
 * Namespace para os controladores da aplicação.
 */
namespace App\Controllers;

use App\Models\Mapa_model;

/**
 * Controller responsável pelo controle do mapa.
 */
class Mapa extends BaseController
{

    /**
     * Objeto do modelo de mapa.
     * @var Mapa_model
     */
    private Mapa_model $mapa_model;

    /**
     * Construtor do controller Mapa.
     * Instancia o modelo de mapa.
     */
    public function __construct()
    {
        // Instancia o modelo de mapa
        $this->mapa_model = new Mapa_model();
    }

    /**
     * Renderiza a view com o cabeçalho, menu e rodapé opcionais.
     *
     * @param string $view O nome da view a ser renderizada.
     * @param mixed|null $data Os dados a serem passados para a view (opcional).
     * @param bool $menu Indica se o menu deve ser incluído ou não (padrão: true).
     * @return string A saída da renderização da view.
     */
    protected function render(string $view, array $data = null, bool $menu = true): string
    {
        // Obtém o nome do controller atual
        $controller = explode('\\', service('router')->controllerName());
        // Obtém o nome do método atual
        $method = service('router')->methodName();

        // Adiciona o nome do controller e o nome do método aos dados
        $data['class']  = end($controller);
        $data['method'] = $method;

        if ($menu) {
            // Renderiza o cabeçalho, menu, view e rodapé com menu
            return view($data['class'] . '/template/header', $data)
                . view($data['class'] . '/template/menu', $data)
                . view($data['class'] . '/' . $view, $data)
                . view($data['class'] . '/template/footer', $data);
        } else {
            // Renderiza o cabeçalho, view e rodapé sem menu
            return view($data['class'] . '/template/header', $data)
                . view($data['class'] . '/' . $view, $data)
                . view($data['class'] . '/template/footer', $data);
        }
    }

    /**
     * Página inicial do mapa.
     */
    public function index()
    {
        $data = array();

        // Renderiza a página 'index' passando os dados
        echo $this->render('index', $data, false);
    }

    /**
     * Obtém os dados dos edifícios e gera um JSON para exibição no mapa.
     *
     * @return void
     */
    public function get_buildings()
    {
        // Obtém os edifícios do modelo de mapa
        $buildings = $this->mapa_model->get_buildings();

        // Array para armazenar os dados dos edifícios
        $dados = array();

        foreach ($buildings as $b) {

            $segments = 360;

            // Gera as coordenadas do círculo para o edifício atual
            $circleCoordinates = $this->generateCircleCoordinates(array($b->latitude, $b->longitude), $b->diametro, $segments);

            // Array para armazenar as coordenadas convertidas
            $coordenadas = array();

            // Converte as coordenadas do círculo para o formato adequado
            foreach ($circleCoordinates as $ponto) {
                $coordenadas[] = [$ponto[0], $ponto[1]];
            }

            // Define os dados do edifício como um recurso do mapa
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
                            "opacity" => 0.7,
                            "name" => $b->name
                        ),
                        "geometry" => array(
                            "type" => "Polygon",
                            "coordinates" => [$coordenadas]
                        ),
                        "type" => "Feature"
                    )
                ]
            );

            // Define os dados do nível do edifício como um recurso do mapa
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
                            "opacity" => 1,
                            "name" => $b->name
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

        // Retorna os dados como JSON
        echo json_encode($dados);
    }

    /**
     * Gera as coordenadas de um círculo com um determinado raio e número de segmentos.
     *
     * @param array $center As coordenadas do centro do círculo [latitude, longitude].
     * @param float $radius O raio do círculo em metros.
     * @param int $segments O número de segmentos em que o círculo será dividido.
     * @return array Um array contendo as coordenadas dos pontos que compõem o círculo.
     */
    public function generateCircleCoordinates(array $center, float $radius, int $segments): array
    {
        // Declaração do array para armazenar as coordenadas
        $coordinates = array();

        // Raio médio da Terra em metros
        $earthRadius = 6371000;

        // Conversão do raio do círculo para radianos considerando a escala da Terra
        $d = $radius / $earthRadius;

        // Loop para calcular as coordenadas de cada segmento do círculo
        for ($i = 0; $i < $segments; $i++) {
            // Cálculo do ângulo em radianos com base no número de segmentos
            $angle = deg2rad(360 / $segments * $i);

            // Cálculo da coordenada X
            $x = $center[0] + rad2deg($d / cos(deg2rad($center[1]))) * cos($angle);

            // Cálculo da coordenada Y
            $y = $center[1] + rad2deg($d) * sin($angle);

            // Adição das coordenadas ao array
            $coordinates[] = [$x, $y];
        }

        // Retorna as coordenadas como um array
        return $coordinates;
    }
}