<?php

    namespace Nartzis;

class Graph
{
    private $size;

    private $nodes;

    private $dict;

        /**
        * Конструктор графа
        *
        * Задаём длинну слов в графе
        * и заполняем пустыми массивами словарь и вершины графа
        *
        * @param int $curSize - длина слов в графе
        * @return object Граф
        */
        public function __construct($curSize = 4)
        {
            $this->size = $curSize;
            $this->nodes = array();
            $this->dict = array();
        }

        /**
        * Загрузка словаря в граф
        *
        * @param array $nodesToLoad - массив слов
        * @return mixed Количество вершин, если загрузка прошла успешно, в противном случае false
        */
        public function loadNodes($nodesToLoad)
        {
            $uniqueNodes = array_unique($nodesToLoad, SORT_STRING);
            $iter = 0;
            if (is_array($uniqueNodes)) {
                foreach ($uniqueNodes as $node) {
                    if (mb_strlen($node, 'UTF-8') == $this->size) {
                        $this->dict[$node] = $iter;
                        $this->nodes[] = array('name'=>$node, 'links'=>array(), 'wave'=>0);
                        $iter++;
                    }
                }
                return $iter;
            } else {
                return false;
            }
        }

        /**
        * Процедура поиска всех соседних вершин графа для заданной вершины
        *
        * @param int $node - вершина для поиска связей
        * @param int $nodeKey - индекс вершины
        * @return null
        */
        private function findLinks($node, $nodeKey)
        {
            foreach ($this->nodes as $curNodeKey => $curNode) {
                if ($nodeKey != $curNodeKey && !in_array($curNodeKey, $node['links'])) {
                    for ($i = 0; $i < $this->size; $i++) {
                        $regexp  = mb_substr($curNode['name'], 0, $i, 'UTF-8');
                        $regexp .= '.';
                        if ($i < $this->size-1) {
                            $regexp .= mb_substr($curNode['name'], $i+1, $this->size, 'UTF-8');
                        }
                        if (mb_ereg($regexp, $node['name'])) {
                            $this->nodes[$nodeKey]['links'][] = $curNodeKey;
                        }
                    }
                }
            }
        }

        /**
        * Процедура построения графа
        *
        * @return null
        */
        private function buildGraph()
        {
            foreach ($this->nodes as $curNodeKey => $curNode) {
                $this->findLinks($curNode, $curNodeKey);
            }
        }

        /**
        * Проверка начальных и конечных слов, перед началом поиска пути
        *
        * @param string $fromWord - начальное слово
        * @param string $toWord - конечное слово
        * @return mixed Сообщение об ошибке или false
        */
        private function checkWords($fromWord, $toWord)
        {
            if ($fromWord == $toWord) {
                return 'Начальные и конечный слова не должны совпадать';
            } elseif (mb_strlen($fromWord, 'UTF-8') != $this->size) {
                return 'Длинна начального слова не соответствует размеру слов графа';
            } elseif (mb_strlen($toWord, 'UTF-8') != $this->size) {
                return 'Длинна конечного слова не соответствует размеру слов графа';
            } elseif (!isset($this->dict[$fromWord])) {
                return 'Начальное слово отсутствует в словаре';
            } elseif (!isset($this->dict[$toWord])) {
                return 'Конечное слово отсутствует в словаре';
            } else {
                return false;
            }
        }

        /**
        * Процедура поиска любого случайного пути в графе
        *
        * @param array $fullList - путь в графе между вершинами
        * @param array $pathArr - текущий путь в графе
        * @param int $fromWordKey - индекс начального слова
        * @param int $toWordKey - индекс конечного слова
        * @return null
        */
        private function getAnyPath(&$fullList, $pathArr, $fromWordKey, $toWordKey)
        {
            $this->findLinks($this->nodes[$fromWordKey], $fromWordKey);
            foreach ($this->nodes[$fromWordKey]['links'] as $link) {
                if (!in_array($link, $pathArr)) {
                    $path = array_merge($pathArr, array($link));
                    if ($link == $toWordKey) {
                        $fullList = $path;
                        break;
                    } else {
                        $this->getAnyPath($fullList, $path, $link, $toWordKey);
                        if (count($fullList) > 0) {
                            break;
                        }
                    }
                }
            }
        }

        /**
        * Поиск любого случайного пути в графе
        *
        * @param string $fromWord - начальное слово
        * @param string $toWord - конечное слово
        * @return mixed Массив вершин графа или сообщение об ошибке
        */
        public function findAnyPath($fromWord, $toWord)
        {
            $errMsg = $this->checkWords($fromWord, $toWord);
            if ($errMsg) {
                return $errMsg;
            } else {
                $path = array();
                $this->getAnyPath($path, array($this->dict[$fromWord]), $this->dict[$fromWord], $this->dict[$toWord]);
                if (empty($path)) {
                    return 'Нет решений';
                } else {
                    return $path;
                }
            }
        }

        /**
        * Процедура поиска кратчайшего пути в графе
        *
        * @param array $fullList - путь в графе между вершинами
        * @param array $pathArr - текущий путь в графе
        * @param int $fromWordKey - индекс начального слова
        * @param int $toWordKey - индекс конечного слова
        * @return null
        */
        private function getShortestPath(&$fullList, $pathArr, $fromWordKey, $toWordKey)
        {
            $this->findLinks($this->nodes[$fromWordKey], $fromWordKey);
            foreach ($this->nodes[$fromWordKey]['links'] as $link) {
                if (!in_array($link, $pathArr)) {
                    $path = array_merge($pathArr, array($link));
                    if (count($fullList) == 0 || count($path) < count($fullList)) {
                        if ($link == $toWordKey) {
                            $fullList = $path;
                        } else {
                            $this->getShortestPath($fullList, $path, $link, $toWordKey);
                        }
                    }
                }
            }
        }

        /**
        * Поиск кратчайшего пути в графе
        *
        * @param string $fromWord - начальное слово
        * @param string $toWord - конечное слово
        * @return mixed Массив вершин графа или сообщение об ошибке
        */
        public function findShortestPath($fromWord, $toWord)
        {
            $errMsg = $this->checkWords($fromWord, $toWord);
            if ($errMsg) {
                return $errMsg;
            } else {
                $path = array();
                $this->getShortestPath($path, array($this->dict[$fromWord]), $this->dict[$fromWord], $this->dict[$toWord]);
                if (empty($path)) {
                    return 'Нет решений';
                } else {
                    return $path;
                }
            }
        }

        /**
        * Процедура поиска кратчайшего пути в графе волновым алгоритмом
        *
        * @param array $fullList - путь в графе между вершинами
        * @param array $pathArr - текущий путь в графе
        * @param int $fromWordKey - индекс начального слова
        * @param int $toWordKey - индекс конечного слова
        * @return null
        */
        private function makeWave($fromWordKey, $toWordKey)
        {
            foreach ($this->nodes as $curNodeKey => $curNode) {
                $this->nodes[$curNodeKey]['wave'] = 0;
            }
            $this->findLinks($this->nodes[$fromWordKey], $fromWordKey);
            $wave = 1;
            $this->nodes[$fromWordKey]['wave'] = $wave;
            $waveArr = $this->nodes[$fromWordKey]['links'];
            while ($this->nodes[$toWordKey]['wave'] == 0 && !empty($waveArr)) {
                $wave++;
                $newWaveArr = array();
                foreach ($waveArr as $link) {
                    if ($this->nodes[$link]['wave'] == 0) {
                        $this->nodes[$link]['wave'] = $wave;
                        $this->findLinks($this->nodes[$link], $link);
                        $newWaveArr = array_unique(array_merge($newWaveArr, $this->nodes[$link]['links']), SORT_REGULAR);
                    }
                }
                $waveArr = $newWaveArr;
            }
            $res = false;
            if ($this->nodes[$toWordKey]['wave'] > 0) {
                $res = true;
            }
            return $res;
        }

        /**
        * Процедура поиска кратчайшего пути в графе волновым алгоритмом
        *
        * @param array $fullList - путь в графе между вершинами
        * @param array $pathArr - текущий путь в графе
        * @param int $fromWordKey - индекс начального слова
        * @param int $toWordKey - индекс конечного слова
        * @return null
        */
        private function getWavePath($fromWordKey, $toWordKey)
        {
            $path = array();
            $path[] = $toWordKey;
            $curKey = $toWordKey;
            $wave = $this->nodes[$toWordKey]['wave'];
            while ($curKey != $fromWordKey && $wave > 0) {
                $wave--;
                foreach ($this->nodes[$curKey]['links'] as $levelKey) {
                    if ($this->nodes[$levelKey]['wave'] == $wave) {
                        $path[] = $levelKey;
                        $curKey = $levelKey;
                        break;
                    }
                }
            }
            return array_reverse($path);
        }

        /**
        * Поиск кратчайшего пути в графе волновым алгоритмом
        *
        * @param string $fromWord - начальное слово
        * @param string $toWord - конечное слово
        * @return mixed Массив вершин графа или сообщение об ошибке
        */
        public function findShortestPathWaveAlgo($fromWord, $toWord)
        {
            $errMsg = $this->checkWords($fromWord, $toWord);
            if ($errMsg) {
                return $errMsg;
            } else {
                $path = array();
                if ($this->makeWave($this->dict[$fromWord], $this->dict[$toWord])) {
                    $path = $this->getWavePath($this->dict[$fromWord], $this->dict[$toWord]);
                    return $path;
                } else {
                    return 'Нет решений';
                }
            }
        }

        /**
        * Печать пути в графе
        *
        * @param int $pathArr - массив вершин для печати
        * @return string Путь в графе по заданным вершинам
        */
        public function printPath($pathArr)
        {
            if (is_array($pathArr)) {
                $res = "";
                foreach ($pathArr as $nodeKey) {
                    if (!empty($res)) {
                        $res .= " -> ";
                    }
                    $res .= $this->nodes[$nodeKey]['name'];
                }
                echo PHP_EOL.$res.PHP_EOL;
            } else {
                echo PHP_EOL.$pathArr.PHP_EOL;
            }
        }

        /**
        * Получение названий вершин графа
        *
        * @param int $pathArr - массив вершин для получения названий
        * @return array Массив названий вершин графа
        */
        public function getPathNames($pathArr)
        {
            $res = array();
            if (is_array($pathArr)) {
                foreach ($pathArr as $nodeKey) {
                    $res[] = $this->nodes[$nodeKey]['name'];
                }
            }
            return $res;
        }

        /**
        * Оптимизация заданного пути, методом отсечения дуг
        *
        * @param int $pathArr - массив вершин для оптимизации
        * @return array Массив оптимизированного пути
        */
        public function optimizePath($pathArr)
        {
            $res = array();
            if (is_array($pathArr)) {
                $cur = 1;
                $prev = 0;
                $max = count($pathArr)-1;
                $res[] = $pathArr[0];
                while ($cur < $max) {
                    // если предыдущий ссылается на следующего - текущий пропускаем
                    if (isset($pathArr[$prev]) && isset($pathArr[$cur]) &&isset($pathArr[$cur+1])) {
                        if (!in_array($pathArr[$cur+1], $this->nodes[$pathArr[$prev]]['links'])) {
                            $res[] = $pathArr[$cur];
                            $prev = $cur;
                        }
                        $cur++;
                    }
                }
                $res[] = $pathArr[$max];
            }
            return $res;
        }
}
