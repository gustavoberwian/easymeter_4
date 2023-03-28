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
$routes->get('/', 'Site::index');
$routes->get('/shopping', 'Shopping::index',['filter'=>'checkApiAuth']);
$routes->get('/shopping/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);
$routes->get('/shopping/(:any)/(:num)', 'Shopping::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/shopping/(:any)', 'Shopping::$1',['filter'=>'checkApiAuth']);
$routes->post('/energia/(:any)', 'Energia::$1',['filter'=>'checkApiAuth']);
$routes->post('/energia/(:any)/(:num)', 'Energia::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/water/(:any)', 'Water::$1',['filter'=>'checkApiAuth']);
$routes->post('/water/(:any)/(:num)', 'Water::$1/$2',['filter'=>'checkApiAuth']);
$routes->post('/user-login','Api\AuthController::UserLogin');
$routes->get('/get-users','Api\ApiController::getUsers',['filter'=>'checkApiAuth']);
$routes->get('/logged-out','Api\AuthController::loggedOut');
$routes->get('/login-view','Api\AuthController::loginView');
$routes->get('/verify-magic-link', 'Api\MagicLinkController::verify');
$routes->post('/verify-magic-link', 'Api\MagicLinkController::updateP');
$routes->get('/update_password', 'Api\UpdatePassword::index');
$routes->get('/api', 'Api::index');
$routes->get('/api/(:any)', 'Api::$1');
$routes->get('/api/(:any)/(:num)', 'Api::$1/$2');
$routes->post('/api', 'Api::index');
$routes->post('/api/(:any)', 'Api::$1');
$routes->post('/api/(:any)/(:num)', 'Api::$1/$2');
$routes->get('/admin', 'Admin::index',['filter'=>'checkApiAuth']);
$routes->get('/admin/(:any)', 'Admin::$1',['filter'=>'checkApiAuth']);
$routes->get('/admin/(:any)/(:num)', 'Admin::$1/$2',['filter'=>'checkApiAuth']);

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
