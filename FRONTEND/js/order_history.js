function openModal(order) {
    document.getElementById('modalOrderId').textContent = order.order_id;
    document.getElementById('modalTotalAmount').textContent = order.total_amount;
    document.getElementById('modalOrderDate').textContent = order.order_date;
    document.getElementById('modalStatus').textContent = order.status;
    
    // Show the modal
    document.getElementById('orderModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
