<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Admin::index');

// Espace opérateur / admin
$routes->get('admin', 'Admin::dashboard');
$routes->get('admin/dashboard', 'Admin::dashboard');
$routes->get('admin/operateurs', 'Admin::operateurs');
$routes->post('admin/operateur', 'Admin::operateurCreate');
$routes->get('admin/operateur/delete/(:num)', 'Admin::operateurDelete/$1');
$routes->get('admin/prefixes', 'Admin::prefixes');
$routes->post('admin/prefixes', 'Admin::prefixeCreate');
$routes->get('admin/prefixe/delete/(:num)', 'Admin::prefixeDelete/$1');

$routes->get('admin/types', 'Admin::types');
$routes->post('admin/types', 'Admin::typeCreate');
$routes->get('admin/type/delete/(:num)', 'Admin::typeDelete/$1');

$routes->get('admin/baremes', 'Admin::baremes');
$routes->post('admin/baremes', 'Admin::baremeCreate');
$routes->get('admin/bareme/delete/(:num)', 'Admin::baremeDelete/$1');

$routes->get('admin/commissions', 'Admin::commissions');
$routes->post('admin/commissions', 'Admin::commissionCreate');
$routes->get('admin/commission/delete/(:num)', 'Admin::commissionDelete/$1');

$routes->get('admin/gains', 'Admin::gains');
$routes->get('admin/comptes', 'Admin::comptes');

// Espace client
$routes->get('client', 'EspaceClient::index');
$routes->get('client/login', 'EspaceClient::login');
$routes->post('client/login', 'EspaceClient::doLogin');
$routes->get('client/logout', 'EspaceClient::logout');
$routes->get('client/dashboard', 'EspaceClient::dashboard');
$routes->get('client/solde', 'EspaceClient::solde');

$routes->get('client/depot', 'EspaceClient::depot');
$routes->post('client/depot', 'EspaceClient::doDepot');
$routes->get('client/retrait', 'EspaceClient::retrait');
$routes->post('client/retrait', 'EspaceClient::doRetrait');
$routes->get('client/transfert', 'EspaceClient::transfert');
$routes->post('client/transfert', 'EspaceClient::doTransfert');
$routes->get('client/frais', 'EspaceClient::frais');
$routes->get('client/historique', 'EspaceClient::historique');
