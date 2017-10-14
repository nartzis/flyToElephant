<?php

    header('Content-type: text/plain; charset=utf-8');

    /* Поиск кратчайшего пути волновым алгоритмом */

    require_once __DIR__ . '/vendor/autoload.php';

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    set_time_limit(0);

    //~ require_once "dict.php";

    require_once "dict.long.php"; // обширный словарь

    $graph = new Nartzis\Graph(4);

    $graph->loadNodes($dict);

    // поиск кратчайшего пути Волновым алгоритмом
    $path = $graph->findShortestPathVaweAlgo("муха", "слон");

    // вывод кратчайшего пути Волновым алгоритмом
    $graph->printPath($path);
