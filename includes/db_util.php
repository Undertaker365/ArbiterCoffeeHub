<?php
// db_util.php - Reusable DB helper functions for Arbiter Coffee Hub
require_once dirname(__DIR__) . '/db_connect.php';

/**
 * Fetch a single row from a query
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function db_fetch_one($sql, $params = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Fetch all rows from a query
 * @param string $sql
 * @param array $params
 * @return array
 */
function db_fetch_all($sql, $params = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Execute a query (insert/update/delete)
 * @param string $sql
 * @param array $params
 * @return bool
 */
function db_execute($sql, $params = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Get last insert ID
 * @return string
 */
function db_last_insert_id() {
    global $conn;
    return $conn->lastInsertId();
}
