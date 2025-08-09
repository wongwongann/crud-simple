<?php
require_once 'includes/functions.php';

if (isset($_GET['restore']) && is_numeric($_GET['restore'])) {
    restoreItem($conn, (int)$_GET['restore']);
    header("Location: trash.php?success=restored");
    exit();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    permanentlyDeleteItem($conn, (int)$_GET['delete']);
    header("Location: trash.php?success=deleted");
    exit();
}

$deletedItems = getDeletedItems($conn);
require_once 'includes/header.php';
?>

<div class="container mt-4">
    <h2><i class="bi bi-trash me-2"></i>Trash Bin</h2>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Serial Number</th>
                <th>Item Name</th>
                <th>Purchase Date</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($deletedItems->num_rows > 0): ?>
                <?php while ($row = $deletedItems->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['tgl_pembelian']) ?></td>
                        <td>
                            <?php if (!empty($row['foto'])): ?>
                                <img src="<?= $row['foto'] ?>" alt="Item Photo" style="max-width:60px;max-height:60px;">
                            <?php else: ?>
                                <span class="text-muted">No photo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Restore and Permanent Delete btn -->
                            <a href="trash.php?restore=<?= $row['id'] ?>" class="btn btn-success btn-sm">Restore</a>
                            <a href="trash.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete Permanently</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Trash is empty.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
?>