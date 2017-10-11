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

	// поиск кратчайшего пути Волновым алгоритмом
	$path = $graph->findShortestPathVaweAlgo("муха", "слон");

	// вывод кратчайшего пути Волновым алгоритмом
	$graph->printPath($path);

?>