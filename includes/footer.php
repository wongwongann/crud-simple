<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        // Add button
        document.getElementById('addItemBtn').addEventListener('click', function() {
            document.getElementById('itemForm').reset();
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check-circle me-1"></i> Add Item';
            document.getElementById('submitBtn').name = 'create';
            itemModal.show();
        });
        
        // Edit button
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const tgl = this.getAttribute('data-tgl');
                const seri = this.getAttribute('data-seri');
                
                document.getElementById('id').value = id;
                document.getElementById('nama_barang').value = nama;
                document.getElementById('tgl_pembelian').value = tgl;
                document.getElementById('nomor_seri').value = seri;
                
                document.getElementById('modalTitle').textContent = 'Edit Item';
                document.getElementById('submitBtn').innerHTML = '<i class="bi bi-save me-1"></i> Update Item';
                document.getElementById('submitBtn').name = 'update';
                itemModal.show();
            });
        });
        
        // Delete button
        let deleteItemId = null;
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function() {
                deleteItemId = this.getAttribute('data-id');
                deleteModal.show();
            });
        });
        
        // Confirm delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteItemId) {
                window.location.href = `?delete=${deleteItemId}`;
            }
        });
        
        // Search function
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryTable tbody tr');
            let visibleRows = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide empty state
            document.getElementById('emptyState').style.display = visibleRows > 0 ? 'none' : 'block';
        });
        
        // notification
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
        
        // Check URL to show notifications
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'added') {
            showNotification('Item added successfully!', 'success');
        } else if (urlParams.get('success') === 'updated') {
            showNotification('Item updated successfully!', 'success');
        } else if (urlParams.get('success') === 'deleted') {
            showNotification('Item deleted successfully!', 'success');
        }

        // Pop up
        const popup = document.getElementById('imagePopup');
        const popupImg = popup.querySelector('img');

        document.querySelectorAll('.item-photo').forEach(img => {
            img.addEventListener('click', function() {
                popupImg.src = this.src;
                popup.style.display = 'flex';
            });
        });

        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                popup.style.display = 'none';
                popupImg.src = '';
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                popup.style.display = 'none';
                popupImg.src = '';
            }
        });
    });
    
    $(document).ready(function() {
        $('#inventoryTable').DataTable({
            responsive: true,
            searching: false,
            lengthMenu: [5, 10, 25, 50, 100],
            pageLength: 10,
            language: {
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    previous: "Prev",
                    next: "Next"
                }
            },
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row mt-3"<"col-sm-6"i><"col-sm-6"p>>'
        });
    });
</script>
</body>
</html>