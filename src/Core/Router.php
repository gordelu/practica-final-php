<?php
namespace App\Core;

// El router guarda una lista de rutas (URL + qué hacer)
// y, cuando llega una petición, busca la que coincide y la ejecuta.
class Router
{
    // Lista de rutas. Cada ruta es un array: ['metodo' => 'GET', 'url' => '/login', 'accion' => función]
    private array $rutas = [];

    // Añade una ruta para peticiones GET (abrir una página)
    public function get(string $url, callable $accion): void
    {
        $this->rutas[] = ['metodo' => 'GET', 'url' => $url, 'accion' => $accion];
    }

    // Añade una ruta para peticiones POST (enviar un formulario)
    public function post(string $url, callable $accion): void
    {
        $this->rutas[] = ['metodo' => 'POST', 'url' => $url, 'accion' => $accion];
    }

    // Busca la ruta que coincide con la petición y ejecuta su acción
    public function dispatch(string $metodo, string $url): void
    {
        // Quita lo que va detrás de "?" -> "/publicaciones?busqueda=php" queda "/publicaciones"
        $url = parse_url($url, PHP_URL_PATH);

        foreach ($this->rutas as $ruta) {
            if ($ruta['metodo'] !== $metodo) {
                continue; // no es el mismo método, pasamos a la siguiente ruta
            }

            // Caso 1: la URL es exactamente igual. Ej: "/login" === "/login"
            if ($ruta['url'] === $url) {
                $ruta['accion']();
                return;
            }

            // Caso 2: la ruta tiene {id}. Ej: "/publicaciones/{id}" con "/publicaciones/5"
            $id = $this->sacarId($ruta['url'], $url);
            if ($id !== null) {
                $ruta['accion']($id);
                return;
            }
        }

        // Ninguna ruta coincide
        http_response_code(404);
        echo 'Página no encontrada';
    }

    // Compara la ruta y la URL trozo a trozo (separando por "/").
    // Si coinciden, devuelve el número que hay en el lugar de {id}. Si no, devuelve null.
    private function sacarId(string $ruta, string $url): ?int
    {
        // "/publicaciones/{id}/editar" -> ['', 'publicaciones', '{id}', 'editar']
        // "/publicaciones/5/editar"    -> ['', 'publicaciones', '5', 'editar']
        $trozosRuta = explode('/', $ruta);
        $trozosUrl = explode('/', $url);

        // Si no tienen el mismo número de trozos, no pueden coincidir
        if (count($trozosRuta) !== count($trozosUrl)) {
            return null;
        }

        $id = null;

        for ($i = 0; $i < count($trozosRuta); $i++) {
            if ($trozosRuta[$i] === '{id}') {
                // En este trozo tiene que haber un número
                if (!is_numeric($trozosUrl[$i])) {
                    return null;
                }
                $id = (int) $trozosUrl[$i];
            } elseif ($trozosRuta[$i] !== $trozosUrl[$i]) {
                // Un trozo normal distinto: no es esta ruta
                return null;
            }
        }

        return $id;
    }
}
