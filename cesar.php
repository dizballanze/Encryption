<?php

interface Crypter {
    public function encrypt($data);
    public function decrypt($data);
}

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


$crypter = new CesarCrypter(16);
$crypted = $crypter->encrypt(file_get_contents('plain1.txt'));
file_put_contents('shifr1.txt', $crypted);
$plain = $crypter->decrypt($crypted);
file_put_contents('open1.txt', $plain);

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

$crypter = new VizhinerCrypter('ШИКАНОВ');
$crypted = $crypter->encrypt(file_get_contents('plain2.txt'));
file_put_contents('shifr2.txt', $crypted);
$plain = $crypter->decrypt($crypted);
file_put_contents('open2.txt', $plain);

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

$crypter = new VizhinerTableCrypter('ШИКАНОВ');
$crypted = $crypter->encrypt(file_get_contents('plain3.txt'));
file_put_contents('shifr3.txt', $crypted);
$plain = $crypter->decrypt($crypted);
file_put_contents('open3.txt', $plain);