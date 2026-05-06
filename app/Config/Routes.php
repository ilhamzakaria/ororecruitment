<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::attemptRegister');
$routes->get('/manage-users', 'UserManagement::index');
$routes->post('/manage-users/toggle-status', 'UserManagement::toggleStatus');
$routes->post('/manage-users/delete', 'UserManagement::delete');
$routes->post('/manage-users/restore', 'UserManagement::restore');
$routes->post('/manage-users/update', 'UserManagement::update');
$routes->post('/manage-users/add', 'UserManagement::add');

$routes->get('/manage-questions', 'QuestionManagement::index/1');
$routes->get('/manage-questions/(:num)', 'QuestionManagement::index/$1');
$routes->post('/manage-questions/add/(:num)', 'QuestionManagement::add/$1');
$routes->post('/manage-questions/update/(:num)', 'QuestionManagement::update/$1');
$routes->post('/manage-questions/delete/(:num)', 'QuestionManagement::delete/$1');
$routes->post('/manage-questions/toggle-status/(:num)', 'QuestionManagement::toggleStatus/$1');
$routes->post('/manage-questions/reorder/(:num)', 'QuestionManagement::reorder/$1');

$routes->get('/tes-interview', 'Home::index');
$routes->post('/tes-interview/check', 'Home::checkStatus');
$routes->post('/tes-interview/complete', 'Home::completeSession');
$routes->post('/tes-interview/save-answer', 'Home::saveAnswer');
$routes->post('/tes-interview/summary', 'Home::getSummary');
$routes->get('/dashboard-user', 'Home::dashboardUser');
$routes->get('/dashboard-hrd', 'Monitoring::dashboard');
$routes->get('/dashboard-hrd/data', 'Monitoring::dashboardData');
$routes->post('/dashboard-hrd/unblock', 'Monitoring::unblockSession');
$routes->post('/monitoring/events', 'Monitoring::recordEvent');
