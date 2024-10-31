function openModal(order) {
    document.getElementById('modalOrderId').textContent = order.order_id;
    document.getElementById('modalTotalAmount').textContent = order.total_amount;
    document.getElementById('modalOrderDate').textContent = order.order_date;
    document.getElementById('modalStatus').textContent = order.status;
    document.getElementById('modalDeliveryAddress').textContent = order.delivery_address || 'N/A';
    document.getElementById('modalDriverName').textContent = order.driver_name || 'Unassigned';
    document.getElementById('modalVehicleType').textContent = order.vehicle_type || 'N/A';
    document.getElementById('modalVehicleNumber').textContent = order.vehicle_number || 'N/A';
    document.getElementById('modalDriverStatus').textContent = order.driver_status || 'unassigned';
    document.getElementById('modalItems').textContent = order.items || 'No items available';

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
