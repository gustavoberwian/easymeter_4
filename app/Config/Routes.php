<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Site');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Route site principal
$routes->get('/', 'Site::index');
$routes->get('/forum', 'Site::forum');
$routes->get('/assuntoforum', 'Site::assuntoforum');

// Routes shopping
$routes->get('/shopping', 'Shopping::index',['filter'=>'checkApiAuth']);
$routes->get('/shopping/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);
$routes->get('/shopping/(:any)/(:num)', 'Shopping::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/shopping/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);

// Routes da Ford para shopping
$routes->get('/ford', 'Shopping::index',['filter'=>'checkApiAuth']);
$routes->get('/ford/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);
$routes->get('/ford/(:any)/(:num)', 'Shopping::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/ford/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);

// Routes da Magalu para shopping
$routes->get('/magalu', 'Shopping::index',['filter'=>'checkApiAuth']);
$routes->get('/magalu/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);
$routes->get('/magalu/(:any)/(:num)', 'Shopping::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/magalu/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);

// Routes da Fras-le para shopping
$routes->get('/frasle', 'Shopping::index',['filter'=>'checkApiAuth']);
$routes->get('/frasle/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);
$routes->get('/frasle/(:any)/(:num)', 'Shopping::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/frasle/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);

// Routes da Consigaz para shopping
$routes->get('/consigaz', 'Consigaz::index',['filter'=>'checkApiAuth']);
$routes->get('/consigaz/(:any)', 'Consigaz::$1',['filter'=>'checkApiAuth']);
$routes->get('/consigaz/(:any)/(:num)', 'Consigaz::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/consigaz/(:any)', 'Consigaz::$1',['filter'=>'checkApiAuth']);

// Routes para grandeza de energia
$routes->post('/energia/(:any)', 'Energia::$1',['filter'=>'checkApiAuth']);
$routes->post('/energia/(:any)/(:num)', 'Energia::$1/$2',['filter'=>'checkApiAuth']);

// Routes para grandeza de água
$routes->post('/water/(:any)', 'Water::$1',['filter'=>'checkApiAuth']);
$routes->post('/water/(:any)/(:num)', 'Water::$1/$2',['filter'=>'checkApiAuth']);

// Routes para grandeza de gás
$routes->post('/gas/(:any)', 'Gas::$1',['filter'=>'checkApiAuth']);
$routes->post('/gas/(:any)/(:num)', 'Gas::$1/$2',['filter'=>'checkApiAuth']);

// Routes para autenticação
$routes->post('/user-login','Api\AuthController::UserLogin');
$routes->get('/get-users','Api\ApiController::getUsers',['filter'=>'checkApiAuth']);
$routes->get('/logged-out','Api\AuthController::loggedOut');
$routes->get('/login-view','Api\AuthController::loginView');
$routes->post('register','Api\RegisterController::registerAction');
$routes->get('register','Api\RegisterController::registerView');
$routes->get('/verify-magic-link', 'Api\MagicLinkController::verify');
$routes->post('/verify-magic-link', 'Api\MagicLinkController::updateP');
$routes->get('/update_password', 'Api\UpdatePassword::index');

// Routes para api easymeter
$routes->get('/api', 'Api::index');
$routes->get('/api/(:any)', 'Api::$1');
$routes->get('/api/(:any)/(:num)', 'Api::$1/$2');
$routes->post('/api', 'Api::index');
$routes->post('/api/(:any)', 'Api::$1');
$routes->post('/api/(:any)/(:num)', 'Api::$1/$2');

// Routes para super admin
$routes->get('/admin', 'Admin::index',['filter'=>'checkApiAuth']);
$routes->get('/admin/(:any)', 'Admin::$1',['filter'=>'checkApiAuth']);
$routes->get('/admin/(:any)/(:num)', 'Admin::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/admin', 'Admin::index',['filter'=>'checkApiAuth']);
$routes->post('/admin/(:any)', 'Admin::$1',['filter'=>'checkApiAuth']);
$routes->post('/admin/(:any)/(:num)', 'Admin::$1/$2',['filter'=>'checkApiAuth']);

// Routes para condomínios
$routes->get('/condominio', 'Condominio::index',['filter'=>'checkApiAuth']);
$routes->get('/condominio/(:any)', 'Condominio::$1',['filter'=>'checkApiAuth']);
$routes->get('/condominio/(:any)/(:num)', 'Condominio::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/condominio/(:any)', 'Condominio::$1',['filter'=>'checkApiAuth']);

// Routes shopping
$routes->get('/industria', 'Industria::index',['filter'=>'checkApiAuth']);
$routes->get('/industria/(:any)', 'Industria::$1',['filter'=>'checkApiAuth']);
$routes->get('/industria/(:any)/(:num)', 'Industria::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/industria/(:any)', 'Industria::$1',['filter'=>'checkApiAuth']);

// Routes da Bauducco para indústria
$routes->get('/bauducco', 'Industria::index',['filter'=>'checkApiAuth']);
$routes->get('/bauducco/(:any)', 'Industria::$1',['filter'=>'checkApiAuth']);
$routes->get('/bauducco/(:any)/(:num)', 'Industria::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/bauducco/(:any)', 'Industria::$1',['filter'=>'checkApiAuth']);

// Routes da Ambev para indústria
$routes->get('/ambev', 'Industria::index',['filter'=>'checkApiAuth']);
$routes->get('/ambev/(:any)', 'Industria::$1',['filter'=>'checkApiAuth']);
$routes->get('/ambev/(:any)/(:num)', 'Industria::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/ambev/(:any)', 'Industria::$1',['filter'=>'checkApiAuth']);

// Routes Mapa
$routes->get('/mapa', 'Mapa::index');
$routes->get('/mapa/(:any)', 'Mapa::$1');
$routes->get('/mapa/(:any)/(:num)', 'Mapa::$1/$2');
$routes->post('/mapa/(:any)', 'Mapa::$1');

service('auth')->routes($routes);

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
