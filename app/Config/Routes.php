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

$routes->get('/manage-questions', 'QuestionManagement::index');
$routes->post('/manage-questions/add', 'QuestionManagement::add');
$routes->post('/manage-questions/update', 'QuestionManagement::update');
$routes->post('/manage-questions/delete', 'QuestionManagement::delete');
$routes->post('/manage-questions/toggle-status', 'QuestionManagement::toggleStatus');

$routes->get('/tes-interview', 'Home::index');
$routes->post('/tes-interview/check', 'Home::checkStatus');
$routes->get('/dashboard-user', 'Home::dashboardUser');
$routes->get('/dashboard-hrd', 'Monitoring::dashboard');
$routes->get('/dashboard-hrd/data', 'Monitoring::dashboardData');
$routes->post('/dashboard-hrd/unblock', 'Monitoring::unblockSession');
$routes->post('/monitoring/events', 'Monitoring::recordEvent');
