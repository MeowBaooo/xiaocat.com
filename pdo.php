<?php
$conn= new PDO("mysql:hotst=127.0.0.1;port=3307;dbname=petlogbook;setchat=utf-8","root","");

function query($res){
    global $conn;
    return $conn->query($res);
}

function fetch($res){
    return $res->fetch();
}

function fetchall($res){
    return $res->fetchall();
}

function rowCount($res){
    return $res->rowCount();
}
?>