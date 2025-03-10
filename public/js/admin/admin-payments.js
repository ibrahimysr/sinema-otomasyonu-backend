
$(document).ready(function() {
    const API_URL = '/api';
    const PAYMENTS_API = `${API_URL}/payments/payment-list`;
    const PAYMENT_ADD_API = `${API_URL}/payments/payment-add`;
    const PAYMENT_UPDATE_API = `${API_URL}/payments/payment-update`;
    const PAYMENT_DELETE_API = `${API_URL}/payments/payment-delete`;
    const TICKETS_API = `${API_URL}/tickets/ticket-list`;
    const USERS_API = `${API_URL}/users/user-list`;

    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const paymentsTableBody = $('#paymentsTableBody');
    
    const addPaymentForm = $('#addPaymentForm');
    const editPaymentForm = $('#editPaymentForm');
    const savePaymentBtn = $('#savePaymentBtn');
    const updatePaymentBtn = $('#updatePaymentBtn');
    const confirmDeletePaymentBtn = $('#confirmDeletePaymentBtn');

    const ticketSelect = $('#ticket_id');
    const userSelect = $('#user_id');
    const editTicketSelect = $('#edit_ticket_id');
    const editUserSelect = $('#edit_user_id');
    
    loadPayments();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        loadPayments();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        loadPayments();
    });
    
    $('#refreshPayments').on('click', function() {
        loadPayments();
    });
    
    $('#printPaymentBtn').on('click', function() {
        printPayment();
    });
    
    function loadPayments() {
        paymentsTableBody.html(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="loading-spinner me-2"></div>
                        <span>Yükleniyor...</span>
                    </div>
                </td>
            </tr>
        `);
        
        const searchParams = {
            user_id: $('#searchUser').val(),
            date: $('#searchDate').val(),
            status: $('#searchStatus').val()
        };
        
        $.ajax({
            url: PAYMENTS_API,
            type: 'GET',
            data: searchParams,
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    displayPayments(response.data);
                    
                    if (response.stats) {
                        updateStats(response.stats);
                    } else {
                        calculateStats(response.data);
                    }
                } else {
                    paymentsTableBody.html(`
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Ödeme bulunamadı
                                </div>
                            </td>
                        </tr>
                    `);
                    
                    updateStats({
                        total: 0,
                        completed: 0,
                        pending: 0,
                        cancelled: 0
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                paymentsTableBody.html(`
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Ödemeler yüklenirken bir hata oluştu
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    function displayPayments(payments) {
        paymentsTableBody.empty();
        
        payments.forEach(payment => {
            const statusClass = getStatusClass(payment.status);
            const statusText = getStatusText(payment.status);
            const paymentMethodText = getPaymentMethodText(payment.payment_method);
            
            const row = $(`
                <tr class="animate__animated animate__fadeIn">
                    <td>${payment.id}</td>
                    <td>${payment.user?.name || 'Bilinmiyor'}</td>
                    <td>${payment.ticket?.ticket_code || 'Bilinmiyor'}</td>
                    <td>${formatCurrency(payment.amount)}</td>
                    <td>${paymentMethodText}</td>
                    <td>${formatDateTime(payment.created_at)}</td>
                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info view-payment" data-id="${payment.id}" title="Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary edit-payment" data-id="${payment.id}" title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-payment" data-id="${payment.id}" title="Sil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            
            paymentsTableBody.append(row);
        });
        
        $('.view-payment').on('click', function() {
            const paymentId = $(this).data('id');
            viewPayment(paymentId);
        });
        
        $('.edit-payment').on('click', function() {
            const paymentId = $(this).data('id');
            editPayment(paymentId);
        });
        
        $('.delete-payment').on('click', function() {
            const paymentId = $(this).data('id');
            $('#delete_payment_id').val(paymentId);
            $('#deletePaymentModal').modal('show');
        });
    }

    function fetchTickets() {
        $.ajax({
            url: TICKETS_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    populateTicketSelects(response.data);
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                showAlert('danger', 'Biletler yüklenirken bir hata oluştu');
            }
        });
    }

    function fetchUsers() {
        $.ajax({
            url: USERS_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    populateUserSelects(response.data);
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                showAlert('danger', 'Kullanıcılar yüklenirken bir hata oluştu');
            }
        });
    }

    function populateTicketSelects(tickets) {
        ticketSelect.html('<option value="">Bilet Seçin</option>');
        editTicketSelect.html('<option value="">Bilet Seçin</option>');
        
        tickets.forEach(ticket => {
            const option = `<option value="${ticket.id}">${ticket.ticket_code} - ${ticket.showtime?.movie?.title || 'Bilinmiyor'}</option>`;
            
            ticketSelect.append(option);
            editTicketSelect.append(option);
        });
    }

    function populateUserSelects(users) {
        userSelect.html('<option value="">Müşteri Seçin</option>');
        editUserSelect.html('<option value="">Müşteri Seçin</option>');
        
        users.forEach(user => {
            const option = `<option value="${user.id}">${user.name} (${user.email})</option>`;
            
            userSelect.append(option);
            editUserSelect.append(option);
        });
        
        const searchUserSelect = $('#searchUser');
        searchUserSelect.html('<option value="">Tümü</option>');
        users.forEach(user => {
            searchUserSelect.append(`<option value="${user.id}">${user.name}</option>`);
        });
    }

    savePaymentBtn.on('click', function() {
        const formData = new FormData(addPaymentForm[0]);
        const paymentData = {
            ticket_id: formData.get('ticket_id'),
            user_id: formData.get('user_id'),
            amount: formData.get('amount'),
            payment_method: formData.get('payment_method'),
            status: formData.get('status'),
            notes: formData.get('notes')
        };
        
        $.ajax({
            url: PAYMENT_ADD_API,
            type: 'POST',
            data: JSON.stringify(paymentData),
            contentType: 'application/json',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success) {
                    $('#addPaymentModal').modal('hide');
                    addPaymentForm[0].reset();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Ödeme başarıyla eklendi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    loadPayments();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Ödeme eklenirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: errors[key][0]
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Ödeme eklenirken bir hata oluştu'
                    });
                }
            }
        });
    });

    function editPayment(paymentId) {
        $.ajax({
            url: `${API_URL}/payments/payment-detail/${paymentId}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const payment = response.data;
                    
                    $('#edit_payment_id').val(payment.id);
                    $('#edit_ticket_id').val(payment.ticket_id);
                    $('#edit_user_id').val(payment.user_id);
                    $('#edit_amount').val(payment.amount);
                    $('#edit_payment_method').val(payment.payment_method);
                    $('#edit_status').val(payment.status);
                    $('#edit_notes').val(payment.notes);
                    
                    fetchTickets();
                    fetchUsers();
                    
                    $('#editPaymentModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Ödeme bilgileri alınırken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Ödeme bilgileri alınırken bir hata oluştu'
                });
            }
        });
    }
    
    function viewPayment(paymentId) {
        $.ajax({
            url: `${API_URL}/payments/payment-detail/${paymentId}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const payment = response.data;
                    
                    $('#paymentCode').text(payment.payment_code || 'Bilinmiyor');
                    $('#paymentStatus').html(`<span class="badge bg-${getStatusClass(payment.status)}">${getStatusText(payment.status)}</span>`);
                    $('#paymentCustomer').text(payment.user?.name || 'Bilinmiyor');
                    $('#paymentTicket').text(payment.ticket?.ticket_code || 'Bilinmiyor');
                    $('#paymentAmount').text(formatCurrency(payment.amount));
                    $('#paymentMethod').text(getPaymentMethodText(payment.payment_method));
                    $('#paymentDate').text(formatDateTime(payment.created_at));
                    $('#paymentNotes').text(payment.notes || 'Not bulunmuyor');
                    
                    $('#viewPaymentModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Ödeme bilgileri alınırken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Ödeme bilgileri alınırken bir hata oluştu'
                });
            }
        });
    }

    updatePaymentBtn.on('click', function() {
        const paymentId = $('#edit_payment_id').val();
        const formData = new FormData(editPaymentForm[0]);
        const paymentData = {
            ticket_id: formData.get('ticket_id'),
            user_id: formData.get('user_id'),
            amount: formData.get('amount'),
            payment_method: formData.get('payment_method'),
            status: formData.get('status'),
            notes: formData.get('notes')
        };
        
        $.ajax({
            url: `${PAYMENT_UPDATE_API}/${paymentId}`,
            type: 'POST',
            data: JSON.stringify(paymentData),
            contentType: 'application/json',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success) {
                    $('#editPaymentModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Ödeme başarıyla güncellendi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    loadPayments();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Ödeme güncellenirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: errors[key][0]
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Ödeme güncellenirken bir hata oluştu'
                    });
                }
            }
        });
    });

    confirmDeletePaymentBtn.on('click', function() {
        const paymentId = $('#delete_payment_id').val();
        
        $.ajax({
            url: `${PAYMENT_DELETE_API}/${paymentId}`,
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success) {
                    $('#deletePaymentModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Ödeme başarıyla silindi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    loadPayments();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Ödeme silinirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Ödeme silinirken bir hata oluştu'
                });
            }
        });
    });
    
    function printPayment() {
        const paymentContent = document.getElementById('viewPaymentModal').querySelector('.modal-body').innerHTML;
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Ödeme Yazdır</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
                <style>
                    body {
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .payment-header {
                        text-align: center;
                        margin-bottom: 20px;
                        padding-bottom: 10px;
                        border-bottom: 1px dashed #ccc;
                    }
                    .payment-details {
                        margin-top: 20px;
                    }
                    .payment-footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 12px;
                        color: #666;
                    }
                    @media print {
                        @page {
                            margin: 0;
                            size: auto;
                        }
                        body {
                            margin: 1cm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="payment-header">
                    <h3>Ödeme Makbuzu</h3>
                    <p>Sinema Otomasyonu</p>
                </div>
                ${paymentContent}
                <div class="payment-footer">
                    <p>Bu makbuz Sinema Otomasyonu tarafından oluşturulmuştur.</p>
                    <p>Tarih: ${new Date().toLocaleDateString('tr-TR')}</p>
                </div>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    };
                </script>
            </body>
            </html>
        `);
        
        printWindow.document.close();
    }

    function formatDateTime(dateTimeStr) {
        if (!dateTimeStr) return null;
        const dateTime = new Date(dateTimeStr);
        return dateTime.toLocaleDateString('tr-TR') + ' ' + dateTime.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount);
    }

    function getStatusClass(status) {
        switch (status) {
            case 'completed':
                return 'success';
            case 'pending':
                return 'warning';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    function getStatusText(status) {
        switch (status) {
            case 'completed':
                return 'Tamamlandı';
            case 'pending':
                return 'Bekliyor';
            case 'cancelled':
                return 'İptal Edildi';
            default:
                return 'Bilinmiyor';
        }
    }
    
    function getPaymentMethodText(method) {
        switch (method) {
            case 'credit_card':
                return 'Kredi Kartı';
            case 'debit_card':
                return 'Banka Kartı';
            case 'cash':
                return 'Nakit';
            case 'transfer':
                return 'Havale/EFT';
            default:
                return 'Bilinmiyor';
        }
    }
    
    function calculateStats(payments) {
        const stats = {
            total: payments.length,
            completed: 0,
            pending: 0,
            cancelled: 0
        };
        
        payments.forEach(payment => {
            switch (payment.status) {
                case 'completed':
                    stats.completed++;
                    break;
                case 'pending':
                    stats.pending++;
                    break;
                case 'cancelled':
                    stats.cancelled++;
                    break;
            }
        });
        
        updateStats(stats);
    }
    
    function updateStats(stats) {
        if (stats) {
            $('#totalPayments').text(stats.total || 0);
            $('#successfulPayments').text(stats.completed || 0);
            $('#pendingPayments').text(stats.pending || 0);
            $('#cancelledPayments').text(stats.cancelled || 0);
        }
    }

    function showAlert(type, message) {
        Swal.fire({
            icon: type === 'success' ? 'success' : 'error',
            title: type === 'success' ? 'Başarılı!' : 'Hata!',
            text: message,
            timer: type === 'success' ? 1500 : undefined,
            showConfirmButton: type !== 'success'
        });
    }
    
    fetchTickets();
    fetchUsers();
}); 