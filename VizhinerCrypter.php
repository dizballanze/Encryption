<?php

/**
 * Алгоритм Вижинера
 */
class VizhinerCrypter implements Crypter {
    private $symbols = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
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
        $this->key = $key;
        for ($i = 0; $i <= mb_strlen($this->symbols, 'UTF-8') - 1; $i++) {
            $this->characters[] = mb_substr($this->symbols, $i, 1, 'UTF-8');
        }
        if (!preg_match('/^[А-Я]+$/u', $key)) {
            throw new Exception('Неправильный ключ');
        }
    }

    public function encrypt($data) {
        $crypted = '';
        for ($i = 0; $i <= mb_strlen($data, 'UTF-8') - 1; $i++) {
            $char_code = (array_search(mb_substr($data, $i, 1, 'UTF-8'), $this->characters)
                + array_search(mb_substr($this->symbols, $i % mb_strlen($this->key, 'UTF-8'), 1, 'UTF-8'), $this->characters))
                % count($this->characters);
            $crypted .= $this->characters[$char_code];
        }
        return $crypted;
    }

    public function decrypt($data) {
        $plain = '';
        for ($i = 0; $i <= mb_strlen($data, 'UTF-8') - 1; $i++) {
            $char_code = (array_search(mb_substr($data, $i, 1, 'UTF-8'), $this->characters)
                - array_search(mb_substr($this->symbols, $i % mb_strlen($this->key, 'UTF-8'), 1, 'UTF-8'), $this->characters))
                % count($this->characters);
            $plain .= $this->characters[$char_code];
        }
        return $plain;
    }
}