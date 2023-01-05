<?php


/*echo preg_match('/reports\/(\d+)/', '/reports/10', $matches).PHP_EOL;
$id = $matches[1];
$route = '/'.explode('/', $matches[0])[0];
echo $route.PHP_EOL;
echo $id.PHP_EOL;
var_dump($matches).PHP_EOL;*/

/*$route = '/reports';
$routeExplode = explode('/', $route);

if (count($routeExplode) == 3) {
    if ($routeExplode[1] == 'reports') {
        preg_match('/^(\/\w+)\/(\d+)$/', $route, $matches);
        $route_base = $matches[1];
        $id = $matches[2];
    }
} elseif (count($routeExplode) == 2) {
    if ($routeExplode[1] == 'reports') {
        $route_base = $route;
    }
} else {
    // Route invalide
    header("HTTP/1.0 404 Not Found");
    http_response_code(404);
}*/



/*echo preg_match('/^(\/\w+)\/(\d+)$/', '/45/1112', $matches).PHP_EOL;
$route_base = $matches[1];
$id = $matches[2];

echo $route_base.PHP_EOL;
echo $id.PHP_EOL;*/

echo preg_match('
/[a-zA-Z]+/[0-9]+
', '/dd/1112', $matches).PHP_EOL;
$route_base = $matches[1];
$id = $matches[1];

echo $route_base.PHP_EOL;
echo $id.PHP_EOL;



