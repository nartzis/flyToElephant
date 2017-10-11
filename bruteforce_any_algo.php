<?php

	header('Content-type: text/plain; charset=utf-8');

	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	set_time_limit(0);

	//~ require_once "dict.php";

	require_once "dict.long.php"; // обширный словарь

	require_once "graph.php";

	$graph = new Graph(4);

	$graph->loadNodes($dict);

	// поиск первого найденного пути методом перебора
	$path = $graph->findAnyPath("муха", "слон");

	// вывод первого найденного пути методом перебора
	$graph->printPath($path);

	// оптимизация пути методом отсечения дуг
	$opath = $graph->optimizePath($path);

	// вывод оптимизированного пути
	$graph->printPath($opath);

?>