// Payment status toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize payment toggles
    const paymentToggles = document.querySelectorAll('.payment-toggle');
    
    paymentToggles.forEach(toggle => {
        toggle.addEventListener('change', handlePaymentToggle);
    });
});

// Format currency for display
const formatCurrency = (amount) => {
    return '$' + parseFloat(amount).toLocaleString('es-ES', {
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2
    });
};

// Handle payment toggle change
function handlePaymentToggle(event) {
    const toggle = event.target;
    const label = toggle.nextElementSibling;
    const originalLabel = label.textContent;
    const element = event.target;
    
    // Get data attributes
    const pedidoId = toggle.getAttribute('data-pedido-id');
    const monto = parseFloat(toggle.getAttribute('data-monto'));
    const wasPaid = toggle.getAttribute('data-nuevo-estado') === 'pagado';
    const nuevoEstado = wasPaid ? 'no_pagado' : 'pagado';
    
    // Get total elements
    const totalPagadoElement = document.getElementById('total-pagado');
    const totalPendienteElement = document.getElementById('total-pendiente');
    
    // Calculate new totals
    let totalPagado = parseCurrencyValue(totalPagadoElement.textContent);
    let totalPendiente = parseCurrencyValue(totalPendienteElement.textContent);
    
    // Update UI immediately
    if (wasPaid) {
        totalPagado -= monto;
        totalPendiente += monto;
    } else {
        totalPagado += monto;
        totalPendiente -= monto;
    }
    
    // Disable toggle while processing
    toggle.disabled = true;
    
    // Update the UI with formatted currency
    totalPagadoElement.textContent = formatCurrency(totalPagado);
    totalPendienteElement.textContent = formatCurrency(totalPendiente);

    // Prepare request data
    const formData = new URLSearchParams();
    formData.append('id', pedidoId);
    formData.append('estado', nuevoEstado);

    // Send AJAX request
    fetch('index.php?c=pedidos&a=cambiarEstadoPago', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(handleResponse)
    .then(data => handleSuccess(data, wasPaid, toggle, label, monto, totalPagadoElement, totalPendienteElement, element))
    .catch(error => handleError(error, wasPaid, monto, totalPagadoElement, totalPendienteElement, toggle, label, originalLabel));
}

// Helper function to parse currency value
function parseCurrencyValue(currencyString) {
    return parseFloat(currencyString.replace(/[^0-9.,]/g, '').replace(',', '.'));
}

// Handle API response
function handleResponse(response) {
    if (!response.ok) {
        throw new Error('Error en la red');
    }
    return response.json();
}

// Handle successful response
function handleSuccess(data, wasPaid, toggle, label, monto, totalPagadoElement, totalPendienteElement, element) {
    if (data.success) {
        // Update the toggle state
        toggle.checked = !wasPaid;
        toggle.setAttribute('data-nuevo-estado', wasPaid ? 'no_pagado' : 'pagado');
        label.textContent = wasPaid ? 'Pendiente' : 'Pagado';
        
        // Update the row's appearance
        const row = element.closest('tr');
        row.classList.toggle('table-success', !wasPaid);
        
        showToast('success', 'Estado de pago actualizado correctamente');
    } else {
        throw new Error(data.message || 'Error al actualizar el estado de pago');
    }
}

// Handle errors
function handleError(error, wasPaid, monto, totalPagadoElement, totalPendienteElement, toggle, label, originalLabel) {
    console.error('Error:', error);
    
    // Revert the changes if there was an error
    let totalPagado = parseCurrencyValue(totalPagadoElement.textContent);
    let totalPendiente = parseCurrencyValue(totalPendienteElement.textContent);
    
    if (wasPaid) {
        totalPagado += monto;
        totalPendiente -= monto;
    } else {
        totalPagado -= monto;
        totalPendiente += monto;
    }
    
    totalPagadoElement.textContent = formatCurrency(totalPagado);
    totalPendienteElement.textContent = formatCurrency(totalPendiente);
    toggle.checked = wasPaid;
    label.textContent = originalLabel;
    
    showToast('danger', 'Error: ' + (error.message || 'No se pudo actualizar el estado de pago'));
}

// Toast notification function
function showToast(type, message) {
    // Check if toast container exists, if not create it
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.position = 'fixed';
        toastContainer.style.top = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show`;
    toast.role = 'alert';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove toast after 5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 150);
    }, 5000);
}

// Add CSS for better visual feedback
const style = document.createElement('style');
style.textContent = `
    .table-success {
        background-color: rgba(25, 135, 84, 0.05) !important;
    }
    .form-switch .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    .form-switch .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    /* Hide any unwanted legend/error message */
    .hidden-legend {
        display: none !important;
    }
    /* Toast styles */
    #toast-container {
        min-width: 300px;
    }
    .alert {
        margin-bottom: 10px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transition: opacity 0.15s ease-in-out;
    }
`;
document.head.appendChild(style);
