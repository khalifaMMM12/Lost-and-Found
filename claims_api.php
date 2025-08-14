<?php
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$action = $_GET['action'] ?? '';

if (isset($_GET['item_id']) && is_numeric($_GET['item_id'])) {
    $item_id = (int)$_GET['item_id'];
    // List claims for an item
    $sql = 'SELECT c.claim_id, c.item_id, c.user_id, c.status, c.claim_name, c.claim_email, c.claim_identifier_type, c.claim_identifier_value, c.created_at,
                   u.user_id AS u_user_id, u.name AS u_name, u.email AS u_email, u.role AS u_role, u.identifier_type AS u_identifier_type, u.identifier_value AS u_identifier_value
            FROM claims c
            JOIN users u ON u.user_id = c.user_id
            WHERE c.item_id = ?
            ORDER BY c.created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $claims = [];
    while ($row = $res->fetch_assoc()) {
        $claims[] = [
            'claim_id' => (int)$row['claim_id'],
            'item_id' => (int)$row['item_id'],
            'user_id' => (int)$row['user_id'],
            'status' => $row['status'],
            'claim_name' => $row['claim_name'],
            'claim_email' => $row['claim_email'],
            'claim_identifier_type' => $row['claim_identifier_type'],
            'claim_identifier_value' => $row['claim_identifier_value'],
            'created_at' => $row['created_at'],
            'user' => [
                'user_id' => (int)$row['u_user_id'],
                'name' => $row['u_name'],
                'email' => $row['u_email'],
                'role' => $row['u_role'],
                'identifier_type' => $row['u_identifier_type'],
                'identifier_value' => $row['u_identifier_value'],
            ]
        ];
    }
    $stmt->close();
    echo json_encode($claims);
    exit;
}

if ($action === 'approve' && isset($_GET['claim_id']) && is_numeric($_GET['claim_id'])) {
    $claim_id = (int)$_GET['claim_id'];
    $stmt = $conn->prepare('UPDATE claims SET status = "approved" WHERE claim_id = ?');
    $stmt->bind_param('i', $claim_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'deny' && isset($_GET['claim_id']) && is_numeric($_GET['claim_id'])) {
    $claim_id = (int)$_GET['claim_id'];
    $stmt = $conn->prepare('UPDATE claims SET status = "denied" WHERE claim_id = ?');
    $stmt->bind_param('i', $claim_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Bad request']);

