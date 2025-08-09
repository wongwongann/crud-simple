<?php
require_once __DIR__ . '/../config/db.php';

// Add
function createItem($conn, $nama_barang, $tgl_pembelian, $nomor_seri, $foto) {
    $foto_path = '';
    if ($foto['name']) {
        $target_dir = __DIR__ . '/../uploads/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $foto_path = 'uploads/' . basename($foto["name"]);
        move_uploaded_file($foto["tmp_name"], __DIR__ . '/../' . $foto_path);
    }

    $sql = "INSERT INTO item_table (nama_barang, tgl_pembelian, nomor_seri, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama_barang, $tgl_pembelian, $nomor_seri, $foto_path);
    $stmt->execute();
    $stmt->close();
}


//update
function updateItem($conn, $id, $nama_barang, $tgl_pembelian, $nomor_seri, $foto) {
    $sql = "UPDATE item_table SET nama_barang = ?, tgl_pembelian = ?, nomor_seri = ?";
    $params = [$nama_barang, $tgl_pembelian, $nomor_seri];
    
    if ($foto['name']) {
        $target_dir = __DIR__ . '/../uploads/';
        $foto_path = 'uploads/' . basename($foto["name"]);
        move_uploaded_file($foto["tmp_name"], __DIR__ . '/../' . $foto_path);
        $sql .= ", foto = ?";
        $params[] = $foto_path;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    $stmt->close();
}


//delete/restore
function deleteItem($conn, $id) {
    $sql = "UPDATE item_table SET is_deleted = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function getItems($conn) {
    $sql = "SELECT * FROM item_table WHERE is_deleted = 0";
    return $conn->query($sql);
}

function getDeletedItems($conn) {
    $sql = "SELECT * FROM item_table WHERE is_deleted = 1";
    return $conn->query($sql);
}

function restoreItem($conn, $id) {
    $sql = "UPDATE item_table SET is_deleted = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function permanentlyDeleteItem($conn, $id) {
    $sql = "SELECT foto FROM item_table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['foto']) && file_exists(__DIR__ . '/../' . $row['foto'])) {
            unlink(__DIR__ . '/../' . $row['foto']);
        }
    }
    $stmt->close();

    $sql = "DELETE FROM item_table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>