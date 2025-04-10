<?php
// Sécurité
if (!defined('ABSPATH')) exit;

/**
 * Connexion PDO sécurisée à la base de données WordPress
 */
function get_pdo_connection()
{
    $host     = DB_HOST;
    $dbname   = DB_NAME;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $charset  = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur PDO : " . $e->getMessage());
    }
}
