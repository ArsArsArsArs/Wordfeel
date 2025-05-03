<?php
    namespace App;

    class User {
        public function __construct(public int $id, public string $username, public string $at) {}
    }
?>