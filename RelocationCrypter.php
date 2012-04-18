<?php

class RelocationCrypter implements Crypter {
    private $symbols = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
    protected $order = array();
    protected $size;

    public function __construct($order_str) {
        $this->order = $this->getOrder($order_str);
        $this->size = count($this->order);
    }

    public function encrypt($data) {
        $data = str_replace('_', '', $data);
        $n = mb_strlen($data, 'UTF-8');
        $mod = $n % $this->size;
        $letters = $this->split_string($this->symbols);
        shuffle($letters);
        if (0 != $mod) {
            for ($i = 0; $i <= $this->size - $mod - 1; $i++) {
                $data .= $letters[$i];
            }
        }

        $blocks_cnt = mb_strlen($data, 'UTF-8') / $this->size;
        $crypted = '';
        for ($i = 0; $i <= $blocks_cnt - 1; $i++) {
            for ($j = 0; $j <= $this->size - 1; $j++) {
                $index = ($i * $this->size) + $this->order[$j];
                $crypted .= mb_substr($data, $index, 1, 'UTF-8');
            }
        }
        return $crypted;
    }

    public function decrypt($data) {
        $blocks_cnt = mb_strlen($data, 'UTF-8') / $this->size;
        $plain = '';
        for ($i = 0; $i <= $blocks_cnt - 1; $i++) {
            for ($j = 0; $j <= $this->size - 1; $j++) {
                $index = ($i * $this->size) + array_search($j, $this->order);
                $plain .= mb_substr($data, $index, 1, 'UTF-8');
            }
        }
        return $plain;
    }

    protected function getOrder($str) {
        $chars = $this->split_string($str);
        $chars = array_unique($chars);
        $order = array();
        foreach ($chars as $char) {
            $order[] = mb_strpos($this->symbols, $char, null, 'UTF-8');
        }

        $ret = array();
        $cnt = count($order);
        for ($i = 0; $i <= $cnt-1; $i++) {
            $min = min($order);
            $min_index = array_search($min, $order);
            $ret[$min_index] = $i;
            unset($order[$min_index]);
        }
        ksort($ret);

        return $ret;
    }

    protected function split_string($str) {
        $characters = array();
        for ($i = 0; $i <= mb_strlen($str, 'UTF-8') - 1; $i++) {
            $characters[$i] = mb_substr($str, $i, 1, 'UTF-8');
        }
        return $characters;
    }
}