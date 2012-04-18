<?php

/**
 * Шифр Цезаря
 */
class CesarCrypter implements Crypter {
    /**
     * Хэш символов и кодов
     * @var Array
     */
    private $characters = array();
    /**
     * Секретный ключ
     * @var int
     */
    protected $key;

    public function __construct($key) {
        $char_code = ord('A');
        for ($i = 0; $i<= 25; $i++) {
            $this->characters[chr($char_code)] = $i;
            $char_code++;
        }
        $this->characters['_'] = 26;
        $this->key = (int) $key;
    }

    public function encrypt($data) {
        $crypted = '';
        $n = count($this->characters);
        for ($i = 0; $i <= strlen($data) - 1; $i++) {
            $char_code = ($this->characters[$data{$i}] + $this->key) % $n;
            $crypted .= array_search($char_code, $this->characters);
        }
        return $crypted;
    }

    public function decrypt($data) {
        $plain = '';
        $n = count($this->characters);
        for ($i = 0; $i <= strlen($data) - 1; $i++) {
            $char_code = ($this->characters[$data{$i}] + $n - $this->key) % $n;
            $plain .= array_search($char_code, $this->characters);
        }
        return $plain;
    }
}