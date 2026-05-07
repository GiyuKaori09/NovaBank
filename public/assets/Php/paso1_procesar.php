<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['temp_reg'] = $_POST;
    header("Location: datos_personales.html");
    exit();
}
