document.addEventListener('DOMContentLoaded', function() {
    // Create modal element
    const modalHTML = `
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="mb-3">Are you sure you want to delete this product?</h5>
                    <p class="mb-1">Product: <span id="productName" class="fw-bold text-danger"></span></p>
                    <p class="mb-4">ID: #<span id="productId"></span></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        This action cannot be undone. The product will be permanently removed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </button>
                    <a href="#" id="deleteProductBtn" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Permanently
                    </a>
                </div>
            </div>
        </div>
    </div>
    `;
    
    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Initialize modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    
    // Get all delete buttons
    document.querySelectorAll('[data-delete-product]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const deleteUrl = `delete_product.php?id=${productId}&confirm=yes`;
            
            // Set modal content
            document.getElementById('productId').textContent = productId;
            document.getElementById('productName').textContent = productName;
            document.getElementById('deleteProductBtn').href = deleteUrl;
            
            // Show modal
            deleteModal.show();
        });
    });
});
