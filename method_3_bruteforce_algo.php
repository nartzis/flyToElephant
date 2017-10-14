<?php

    header('Content-type: text/plain; charset=utf-8');

    /* Поиск кратчайшего пути методом перебора */

    require_once __DIR__ . '/vendor/autoload.php';

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    set_time_limit(0);

    require_once "dict.php"; // краткий словарь

    //~ require_once "dict.long.php"; // обширный словарь - поиск потребует слишком много времени

    $graph = new Nartzis\Graph(4);

    $graph->loadNodes($dict);

    // поиск кратчайшего пути методом перебора
    $path = $graph->findShortestPath("муха", "слон");

    // вывод кратчайшего пути методом перебора
    $graph->printPath($path);
