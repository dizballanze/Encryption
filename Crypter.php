<?php

interface Crypter {
    public function encrypt($data);
    public function decrypt($data);
}