$(document).ready(function() {
    const API_URL = '/api';
    const PAYMENTS_API = `${API_URL}/payments/payment-list`;
    const PAYMENTS_DATATABLE_API = `${API_URL}/payments/datatable`;
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

    const table = $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false, 
        ajax: {
            url: PAYMENTS_DATATABLE_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: function(d) {
                d.user_id = $('#searchUser').val();
                d.date = $('#searchDate').val();
                d.status = $('#searchStatus').val();
                d.payment_method = $('#searchPaymentMethod').val();
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Veriler yüklenirken bir hata oluştu: ' + xhr.statusText
                    });
                }
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'ticket_code', name: 'ticket.ticket_code', orderable: false },
            { data: 'customer', name: 'user.name', orderable: false },
            { data: 'amount', name: 'amount', orderable: true },
            { data: 'payment_method', name: 'payment_method', orderable: true },
            { data: 'payment_date', name: 'created_at', orderable: true },
            { data: 'status', name: 'status', orderable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '/js/i18n/tr.json' 
        },
        drawCallback: function() {
            $('#paymentsTable tbody tr').addClass('animate__animated animate__fadeIn');
            updateStats();
        }
    });
    
    const addPaymentForm = $('#addPaymentForm');
    const editPaymentForm = $('#editPaymentForm');
    const savePaymentBtn = $('#savePaymentBtn');
    const updatePaymentBtn = $('#updatePaymentBtn');
    const confirmDeletePaymentBtn = $('#confirmDeletePaymentBtn');

    const ticketSelect = $('#ticket_id');
    const userSelect = $('#user_id');
    const editTicketSelect = $('#edit_ticket_id');
    const editUserSelect = $('#edit_user_id');
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });
    
    $('#searchUser, #searchStatus, #searchPaymentMethod').on('change', function() {
        table.draw();
    });
    
    $('#searchDate').on('change', function() {
        table.draw();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        table.draw();
    });
    
    $('#refreshPayments').on('click', function() {
        table.draw();
    });
    
    $('#printPaymentBtn').on('click', function() {
        printPayment();
    });
    
    function fetchTickets(selectedTicketId = null) {
        $.ajax({
            url: TICKETS_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    populateTicketSelects(response.data, selectedTicketId);
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

    function fetchUsers(selectedUserId = null) {
        $.ajax({
            url: USERS_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    populateUserSelects(response.data, selectedUserId);
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

    function populateTicketSelects(tickets, selectedTicketId = null) {
        ticketSelect.html('<option value="">Bilet Seçin</option>');
        editTicketSelect.html('<option value="">Bilet Seçin</option>');
        
        tickets.forEach(ticket => {
            const option = `<option value="${ticket.id}" ${selectedTicketId && selectedTicketId == ticket.id ? 'selected' : ''}>${ticket.ticket_code} - ${ticket.showtime?.movie?.title || 'Bilinmiyor'}</option>`;
            
            ticketSelect.append(option);
            editTicketSelect.append(option);
        });
        
        if (selectedTicketId) {
            editTicketSelect.val(selectedTicketId);
        }
    }

    function populateUserSelects(users, selectedUserId = null) {
        userSelect.html('<option value="">Müşteri Seçin</option>');
        editUserSelect.html('<option value="">Müşteri Seçin</option>');
        
        users.forEach(user => {
            const option = `<option value="${user.id}" ${selectedUserId && selectedUserId == user.id ? 'selected' : ''}>${user.name} (${user.email})</option>`;
            
            userSelect.append(option);
            editUserSelect.append(option);
        });
        
        if (selectedUserId) {
            editUserSelect.val(selectedUserId);
        }
        
        const searchUserSelect = $('#searchUser');
        searchUserSelect.html('<option value="">Tümü</option>');
        users.forEach(user => {
            searchUserSelect.append(`<option value="${user.id}">${user.name}</option>`);
        });
    }

    savePaymentBtn.on('click', function() {
        const formData = new FormData(addPaymentForm[0]);
        
        const ticketId = formData.get('ticket_id');
        if (!ticketId) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Lütfen bir bilet seçin.'
            });
            return;
        }
        
        const paymentMethod = formData.get('payment_method');
        if (!paymentMethod) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Lütfen bir ödeme yöntemi seçin.'
            });
            return;
        }
        
        const validPaymentMethods = ['credit_card', 'debit_card', 'cash', 'transfer'];
        if (!validPaymentMethods.includes(paymentMethod)) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Ödeme yöntemi geçerli değil. Geçerli değerler: credit_card, debit_card, cash, transfer.'
            });
            return;
        }
        
        const status = formData.get('status');
        if (!status) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Lütfen bir durum seçin.'
            });
            return;
        }
        
        const validStatuses = ['pending', 'completed', 'failed'];
        
        let finalStatus = status;
        if (status === 'cancelled') {
            finalStatus = 'failed';
        }
        
        if (!validStatuses.includes(finalStatus)) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Durum geçerli değil. Geçerli değerler: pending, completed, failed.'
            });
            return;
        }
        
        const paymentData = {
            ticket_id: parseInt(ticketId),
            payment_method: paymentMethod,
            status: finalStatus
        };
        
        const userId = formData.get('user_id');
        if (userId) {
            paymentData.user_id = parseInt(userId);
        }
        
        const amount = formData.get('amount');
        if (amount && amount.trim() !== '') {
            paymentData.amount = parseFloat(amount);
        }
        
        const notes = formData.get('notes');
        if (notes && notes.trim() !== '') {
            paymentData.notes = notes;
        }
        
        console.log('Gönderilen veri:', paymentData);
        
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
                    
                    table.draw();
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
                    
                    $('#editPaymentModal').modal('show');
                    
                    const paymentData = {
                        id: payment.id,
                        ticket_id: payment.ticket_id,
                        user_id: payment.user_id,
                        amount: payment.amount,
                        payment_method: payment.payment_method,
                        status: payment.status,
                        notes: payment.notes
                    };
                    
                    $('#edit_payment_id').val(paymentData.id);
                    $('#edit_amount').val(paymentData.amount);
                    $('#edit_payment_method').val(paymentData.payment_method);
                    $('#edit_status').val(paymentData.status);
                    $('#edit_notes').val(paymentData.notes);
                    
                    fetchTickets(paymentData.ticket_id);
                    fetchUsers(paymentData.user_id);
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
        
        const ticketId = formData.get('ticket_id');
        if (!ticketId) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Lütfen bir bilet seçin.'
            });
            return;
        }
        
        const paymentMethod = formData.get('payment_method');
        if (!paymentMethod) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Lütfen bir ödeme yöntemi seçin.'
            });
            return;
        }
        
        const validPaymentMethods = ['credit_card', 'debit_card', 'cash', 'transfer'];
        if (!validPaymentMethods.includes(paymentMethod)) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Ödeme yöntemi geçerli değil. Geçerli değerler: credit_card, debit_card, cash, transfer.'
            });
            return;
        }
        
        const status = formData.get('status');
        if (!status) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Lütfen bir durum seçin.'
            });
            return;
        }
        
        const validStatuses = ['pending', 'completed', 'failed'];
        
        let finalStatus = status;
        if (status === 'cancelled') {
            finalStatus = 'failed';
        }
        
        if (!validStatuses.includes(finalStatus)) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Durum geçerli değil. Geçerli değerler: pending, completed, failed.'
            });
            return;
        }
        
        const paymentData = {
            ticket_id: parseInt(ticketId),
            payment_method: paymentMethod,
            status: finalStatus
        };
        
        const userId = formData.get('user_id');
        if (userId) {
            paymentData.user_id = parseInt(userId);
        }
        
        const amount = formData.get('amount');
        if (amount && amount.trim() !== '') {
            paymentData.amount = parseFloat(amount);
        }
        
        const notes = formData.get('notes');
        if (notes && notes.trim() !== '') {
            paymentData.notes = notes;
        }
        
        console.log('Güncellenen veri:', paymentData);
        
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
                    
                    table.draw();
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
                    
                    table.draw();
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
    
    function updateStats() {
        $.ajax({
            url: `${API_URL}/payments/stats`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const stats = response.data;
                    $('#totalPayments').text(stats.total || 0);
                    $('#successfulPayments').text(stats.completed || 0);
                    $('#pendingPayments').text(stats.pending || 0);
                    $('#cancelledPayments').text(stats.cancelled || 0);
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                }
            }
        });
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
    
    // DataTables için event listener'lar
    $('#paymentsTable').on('click', '.view-payment', function() {
        const paymentId = $(this).data('id');
        viewPayment(paymentId);
    });
    
    $('#paymentsTable').on('click', '.edit-payment', function() {
        const paymentId = $(this).data('id');
        editPayment(paymentId);
    });
    
    $('#paymentsTable').on('click', '.delete-payment', function() {
        const paymentId = $(this).data('id');
        $('#delete_payment_id').val(paymentId);
        $('#deletePaymentModal').modal('show');
    });
    
    fetchTickets();
    fetchUsers();
}); 