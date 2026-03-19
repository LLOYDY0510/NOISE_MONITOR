<?php
function getDB() {
    return new PDO("mysql:host=localhost;dbname=libraryquiet", "root", "");
}