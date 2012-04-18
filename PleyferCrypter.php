<?php
/**
 * Шифр Плейфейера
 */
class PleyferCrypter implements Crypter {
    protected $matrix = array();
    protected $key;
    private $characters = 'ABCDEFGHIKLMNOPQRSTUVWXYZ';

    public function __construct($key) {
        $this->key = str_replace('J', 'I', $key);
        $this->initMatrix();
    }

    public function encrypt($data) {
        $data = str_replace('J', 'I', $data);
        $alphabet = $this->split_string($this->characters);
        foreach ($alphabet as $letter) {
            $data = str_replace(str_repeat($letter, 2), $letter . 'X' . $letter, $data);
        }

        if ((strlen($data) % 2) == 1)
            $data .= 'X';
        $bygramms = str_split($data, 2);

        $crypted = '';
        foreach ($bygramms as $par) {
            $first = $this->findInMatrix($par{0});
            $second = $this->findInMatrix($par{1});
            $crypted .= $this->getCodedPair($first, $second);
        }
        return $crypted;
    }

    public function decrypt($data) {
        $bygramms = str_split($data, 2);

        $plain = '';
        foreach ($bygramms as $par) {
            $first = $this->findInMatrix($par{0});
            $second = $this->findInMatrix($par{1});
            $plain .= $this->getPlainPair($first, $second);
        }
        return $plain;
    }

    public function getMatrix() {
        return $this->matrix;
    }

    protected function getPlainPair(Array $first, Array $second) {
        if ($first['row'] == $second['row']) {
            $first_col = $first['col'] - 1;
            if ($first_col < 0) $first_col = 4;
            $second_col = $second['col'] - 1;
            if ($second_col < 0) $second_col = 4;
            return $this->matrix[$first['row']][$first_col]
                 . $this->matrix[$first['row']][$second_col];
        } elseif ($first['col'] == $second['col']) {
            $first_row = $first['row'] - 1;
            if ($first_row < 0) $first_row = 4;
            $second_row = $second['row'] - 1;
            if ($second_row < 0) $second_row = 4;
            return $this->matrix[$first_row][$first['col']]
                . $this->matrix[$second_row][$second['col']];
        } else {
            return $this->matrix[$second['row']][$first['col']]
                 . $this->matrix[$first['row']][$second['col']];
        }
    }

    protected function getCodedPair(Array $first, Array $second) {
        if ($first['row'] == $second['row']) {
            return $this->matrix[$first['row']][($first['col'] + 1) % 5]
                 . $this->matrix[$first['row']][($second['col'] + 1) % 5];
        } elseif ($first['col'] == $second['col']) {
            return $this->matrix[($first['row'] + 1) % 5][$first['col']]
                . $this->matrix[($second['row'] + 1) % 5][$second['col']];
        } else {
            return $this->matrix[$second['row']][$first['col']]
                 . $this->matrix[$first['row']][$second['col']];
        }
    }

    protected function findInMatrix($letter) {
        for ($i = 0; $i <= 4; $i++) {
            for ($j = 0; $j <= 4; $j++) {
                if ($this->matrix[$i][$j] == $letter) {
                    return array('row' => $i, 'col' => $j);
                }
            }
        }
    }

    protected function initMatrix() {
        $alphabet = $this->split_string($this->characters);
        $chars = str_split($this->key);
        $chars = array_unique($chars);
        for ($i = 0; $i <= 4; $i++) {
            $this->matrix[$i] = array();
            for ($j = 0; $j <= 4; $j++) {
                $index = $i *5 + $j;
                if (isset($chars[$index])) {
                    $this->matrix[$i][$j] = $chars[$index];
                    unset($alphabet[array_search($chars[$index], $alphabet)]);
                } else {
                    break 2;
                }
            }
        }

        foreach ($alphabet as $letter) {
            $this->matrix[$i][$j] = $letter;

            $j++;
            if ($j > 4) {$i++;$j=0;}
            if ($i > 4) break;
        }
    }

    protected function split_string($str) {
        $characters = array();
        for ($i = 0; $i <= mb_strlen($str, 'UTF-8') - 1; $i++) {
            $characters[$i] = mb_substr($str, $i, 1, 'UTF-8');
        }
        return $characters;
    }
}