<?php
include 'Crypter.php';
include 'CesarCrypter.php';
include 'VizhinerCrypter.php';
include 'VizhinerTableCrypter.php';

// Алгоритм Цезаря
$crypter = new CesarCrypter(16);
$crypted = $crypter->encrypt(file_get_contents('plain1.txt'));
file_put_contents('shifr1.txt', $crypted);
$plain = $crypter->decrypt($crypted);
file_put_contents('open1.txt', $plain);

// Алгоритм Вижинера
$crypter = new VizhinerCrypter('ШИКАНОВ');
$crypted = $crypter->encrypt(file_get_contents('plain2.txt'));
file_put_contents('shifr2.txt', $crypted);
$plain = $crypter->decrypt($crypted);
file_put_contents('open2.txt', $plain);

// Таблицы вижинера
$crypter = new VizhinerTableCrypter('ШИКАНОВ');
$crypted = $crypter->encrypt(file_get_contents('plain3.txt'));
file_put_contents('shifr3.txt', $crypted);
$plain = $crypter->decrypt($crypted);
file_put_contents('open3.txt', $plain);