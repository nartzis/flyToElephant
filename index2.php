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

	// вывод первого найденного пути
	$path = $graph->findAnyPath("муха", "слон");

	$graph->printPath($path);

	// получаем новый словарь по первому пути
	$newDict = $graph->getPathNames($path);

	$graph2 = new Graph(4);

	$graph2->loadNodes($newDict);

	// вывод кратчайшего пути
	$path = $graph2->findShortestPath("муха", "слон");

	$graph2->printPath($path);

?>