<?php

$f3 = require('f3/base.php');

$f3->set('AUTOLOAD','classes/');

/*** GET ***/
$f3->route('GET /', 'Api->get_todos');
$f3->route('GET /completed', 'Api->get_completed');
$f3->route('GET /uncompleted', 'Api->get_uncompleted');
$f3->route('GET /@id', 'Api->get_todo');
$f3->route('GET /user/@id', 'Api->get_todos_by_user_id');

/*** POST ***/
$f3->route('POST /', 'Api->post_todos');

/*** PUT ***/
$f3->route('PUT /@id', 'Api->update_todo');

/*** DELETE ***/
$f3->route('DELETE /@id', 'Api->delete_todo');
$f3->route('DELETE /user/@id', 'Api->delete_todos_by_user_id');
$f3->route('DELETE /delete/all', 'Api->delete_all');
$f3->route('DELETE /delete/@id', 'Api->delete_todo');

$f3->run();
