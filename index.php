<?php
require_once 'includes/functions.php';

// form
if (isset($_POST['create'])) {
    createItem($conn, $_POST['nama_barang'], $_POST['tgl_pembelian'], $_POST['nomor_seri'], $_FILES['foto']);
    header("Location: index.php?success=added");
    exit();
}

if (isset($_POST['update'])) {
    updateItem($conn, $_POST['id'], $_POST['nama_barang'], $_POST['tgl_pembelian'], $_POST['nomor_seri'], $_FILES['foto']);
    header("Location: index.php?success=updated");
    exit();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    deleteItem($conn, (int)$_GET['delete']);
    header("Location: index.php?success=deleted");
    exit();
}

// Fetch items display
$items = getItems($conn);

require_once 'includes/header.php';
?>

<div class="container mt-4">

<div class="image-popup" id="imagePopup" style="display:none;">
    <img src="" alt="Preview" />
</div>

    <!-- Search/Add Item  -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Inventory Items</h5>
            <div class="d-flex">
                <div class="search-container me-3">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search items...">
                </div>
                <button type="button" class="btn btn-primary" id="addItemBtn">
                    <i class="bi bi-plus-circle me-1"></i> Add Item
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover" id="inventoryTable">
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
                        <?php if ($items->num_rows > 0): ?>
                            <?php while ($row = $items->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['tgl_pembelian']) ?></td>
                                    <td>
                                        <?php if (!empty($row['foto'])): ?>
                                            <img src="<?= $row['foto'] ?>" alt="Item Photo" class="item-photo">
                                        <?php else: ?>
                                            <span class="text-muted">No photo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info editBtn"
                                            data-id="<?= $row['id'] ?>"
                                            data-seri="<?= htmlspecialchars($row['nomor_seri']) ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_barang']) ?>"
                                            data-tgl="<?= htmlspecialchars($row['tgl_pembelian']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger deleteBtn"
                                            data-id="<?= $row['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Nothing found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="empty-state" id="emptyState" style="display: none;">
                    <i class="bi bi-inbox"></i>
                    <h4>Nothing found</h4>
                    <p>Try search for something else</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data" id="itemForm">
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="nomor_seri" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="nomor_seri" name="nomor_seri" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="tgl_pembelian" class="form-label">Purchase Date</label>
                        <input type="date" class="form-control" id="tgl_pembelian" name="tgl_pembelian" required>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Item Photo</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <div class="form-text">Upload a photo of the item (optional)</div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create" id="submitBtn" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Add Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalTitle">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification -->
<div class="notification" id="notification"></div>

</script>
<?php
require_once 'includes/footer.php';
$conn->close();
?>