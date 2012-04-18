<?php

/**
 * Таблица Вижинера
 */
class VizhinerTableCrypter implements  Crypter {
    private $symbols = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ_';
    /**
     * Таблица Вижинера
     * @var Array
     */
    private $table = array();
    /**
     * Секретный ключ
     * @var int
     */
    protected $key;

    public function __construct($lozung) {
        $str = $this->symbols;
        for ($i = 0; $i <= mb_strlen($this->symbols, 'UTF-8') - 1; $i++) {
            $row = $this->table[] = $this->split_string($str);
            $first = array_shift($row);
            array_push($row, $first);
            $str = implode('', $row);
        }
        $this->key = $lozung;
    }

    public function encrypt($data) {
        $n = mb_strlen($data, 'UTF-8');
        $crypted = '';
        for ($i = 0; $i <= $n - 1; $i++) {
            $curr = mb_substr($data, $i, 1, 'UTF-8');
            $row_number = mb_strpos($this->symbols, $curr, null, 'UTF-8');
            $col_symbol = mb_substr($this->key, $i % mb_strlen($this->key, 'UTF-8'), 1, 'UTF-8');
            $col_number = mb_strpos($this->symbols, $col_symbol, null, 'UTF-8');
            $crypted .= $this->table[$row_number][$col_number];
        }
        return $crypted;
    }

    public function decrypt($data) {
        $n = mb_strlen($data, 'UTF-8');
        $plain = '';
        for ($i = 0; $i <= $n - 1; $i++) {
            $curr = mb_substr($data, $i, 1, 'UTF-8');
            $col_symbol = mb_substr($this->key, $i % mb_strlen($this->key, 'UTF-8'), 1, 'UTF-8');
            $col_number = mb_strpos($this->symbols, $col_symbol, null, 'UTF-8');

            foreach ($this->table as $row_number => $row) {
                if ($row[$col_number] == $curr)
                    break;
            }

            $plain .= mb_substr($this->symbols, $row_number, 1, 'UTF-8');
        }
        return $plain;
    }

    protected function split_string($str) {
        $characters = array();
        for ($i = 0; $i <= mb_strlen($str, 'UTF-8') - 1; $i++) {
            $characters[$i] = mb_substr($str, $i, 1, 'UTF-8');
        }
        return $characters;
    }
}