<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array();



$routes['/']='index#index';
$routes['/index']='index#index';
$routes['/report1']='index#report1';
$routes['/borrowing']='index#index';
$routes['/queryborrowing']='index#borrowing';

$routes['/getAllBorrowingData']='index#getAllBorrowingData';
$routes['/getQueryBorrowingData']='index#getQueryBorrowingData';
$routes['/getReport1']='index#getReport1';