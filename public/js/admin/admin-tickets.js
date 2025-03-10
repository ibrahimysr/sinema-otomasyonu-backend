$(document).ready(function() {
    const API_URL = '/api';
    const TICKETS_API = `${API_URL}/tickets/ticket-list`;
    const TICKETS_DATATABLE_API = `${API_URL}/tickets/datatable`;
    const TICKET_ADD_API = `${API_URL}/tickets/ticket-add`;
    const TICKET_UPDATE_API = `${API_URL}/tickets/ticket-update`;
    const TICKET_DELETE_API = `${API_URL}/tickets/ticket-delete`;
    const SHOWTIMES_API = `${API_URL}/showtimes/showtime-list`;
    const USERS_API = `${API_URL}/users/user-list`;
    const MOVIES_API = `${API_URL}/movies/all-movies`;

    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const table = $('#ticketsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false, 
        ajax: {
            url: TICKETS_DATATABLE_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: function(d) {
                d.ticket_code = $('#searchTicketCode').val();
                d.movie_id = $('#searchMovie').val();
                d.date = $('#searchDate').val();
                d.status = $('#searchStatus').val();
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
            { data: 'ticket_code', name: 'ticket_code', orderable: false },
            { data: 'movie', name: 'showtime.movie.title', orderable: false },
            { data: 'cinema', name: 'showtime.cinemaHall.cinema.name', orderable: false },
            { data: 'hall', name: 'showtime.cinemaHall.name', orderable: false },
            { data: 'showtime', name: 'showtime.start_time', orderable: true },
            { data: 'seat', name: 'seat_number', orderable: false },
            { data: 'customer', name: 'user.name', orderable: false },
            { data: 'price', name: 'price', orderable: true },
            { data: 'status', name: 'status', orderable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '/js/i18n/tr.json' 
        },
        drawCallback: function() {
            $('#ticketsTable tbody tr').addClass('animate__animated animate__fadeIn');
            updateStats();
        }
    });
    
    const addTicketForm = $('#addTicketForm');
    const editTicketForm = $('#editTicketForm');
    const saveTicketBtn = $('#saveTicketBtn');
    const updateTicketBtn = $('#updateTicketBtn');
    const confirmDeleteTicketBtn = $('#confirmDeleteTicketBtn');

    const showtimeSelect = $('#showtime_id');
    const userSelect = $('#user_id');
    const editShowtimeSelect = $('#edit_showtime_id');
    const editUserSelect = $('#edit_user_id');
    
    loadMovies();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });
    
    $('#searchTicketCode').on('keyup', function() {
        table.draw();
    });
    
    $('#searchMovie, #searchStatus').on('change', function() {
        table.draw();
    });
    
    $('#searchDate').on('change', function() {
        table.draw();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        table.draw();
    });
    
    $('#refreshTickets').on('click', function() {
        table.ajax.reload();
    });
    
    $('#printTicketBtn').on('click', function() {
        printTicket();
    });
    
    function updateStats() {
        $.ajax({
            url: TICKETS_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    calculateStats(response.data);
                }
            }
        });
    }
    
    $(document).on('click', '.view-ticket', function() {
        const ticketId = $(this).data('id');
        viewTicket(ticketId);
    });
    
    $(document).on('click', '.edit-ticket', function() {
        const ticketId = $(this).data('id');
        editTicket(ticketId);
    });
    
    $(document).on('click', '.delete-ticket', function() {
        const ticketId = $(this).data('id');
        const ticketCode = $(this).data('code');
        confirmDelete(ticketId, ticketCode);
    });
    
    saveTicketBtn.on('click', function() {
        saveTicket();
    });
    
    updateTicketBtn.on('click', function() {
        updateTicket();
    });
    
    confirmDeleteTicketBtn.on('click', function() {
        deleteTicket();
    });
    
    function loadMovies() {
        $.ajax({
            url: MOVIES_API,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    const movieSelect = $('#searchMovie');
                    movieSelect.find('option:not(:first)').remove();
                    
                    response.data.forEach(movie => {
                        movieSelect.append(`<option value="${movie.id}">${movie.title}</option>`);
                    });
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

    function fetchShowtimes(selectedShowtimeId = null) {
        fetch(SHOWTIMES_API, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Seanslar getirilemedi');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data.length > 0) {
                populateShowtimeSelects(data.data, selectedShowtimeId);
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            showAlert('danger', 'Seanslar yüklenirken bir hata oluştu');
        });
    }

    function fetchUsers(selectedUserId = null) {
        fetch(USERS_API, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Kullanıcılar getirilemedi');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data.length > 0) {
                populateUserSelects(data.data, selectedUserId);
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            showAlert('danger', 'Kullanıcılar yüklenirken bir hata oluştu');
        });
    }

    function populateShowtimeSelects(showtimes, selectedShowtimeId = null) {
        showtimeSelect.html('<option value="">Seans Seçin</option>');
        editShowtimeSelect.html('<option value="">Seans Seçin</option>');
        
        showtimes.forEach(showtime => {
            const option = `<option value="${showtime.id}" ${selectedShowtimeId && selectedShowtimeId == showtime.id ? 'selected' : ''}>${showtime.movie?.title} - ${showtime.cinema_hall?.cinema?.name} - ${showtime.cinema_hall?.name} - ${formatDateTime(showtime.start_time)}</option>`;
            
            showtimeSelect.append(option);
            editShowtimeSelect.append(option);
        });
        
        if (selectedShowtimeId) {
            editShowtimeSelect.val(selectedShowtimeId);
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
    }

    function viewTicket(ticketId) {
        $.ajax({
            url: `${API_URL}/tickets/ticket-detail/${ticketId}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const ticket = response.data;
                    
                    if (ticket.showtime?.movie?.poster_url) {
                        $('#ticketMoviePoster').html(`
                            <img src="${ticket.showtime.movie.poster_url}" alt="${ticket.showtime.movie.title}" class="img-fluid rounded">
                        `);
                    } else {
                        $('#ticketMoviePoster').html(`
                            <div class="no-poster">
                                <i class="fas fa-film fa-5x text-muted"></i>
                            </div>
                        `);
                    }
                    
                    $('#ticketMovieTitle').text(ticket.showtime?.movie?.title || 'Bilinmiyor');
                    
                    $('#ticketCode').text(ticket.ticket_code || 'Bilinmiyor');
                    $('#ticketStatus').html(`<span class="badge bg-${getStatusClass(ticket.status)}">${getStatusText(ticket.status)}</span>`);
                    $('#ticketCinema').text(ticket.showtime?.cinema_hall?.cinema?.name || 'Bilinmiyor');
                    $('#ticketHall').text(ticket.showtime?.cinema_hall?.name || 'Bilinmiyor');
                    
                    if (ticket.showtime?.start_time) {
                        const dateTime = new Date(ticket.showtime.start_time);
                        $('#ticketDate').text(dateTime.toLocaleDateString('tr-TR'));
                        $('#ticketTime').text(dateTime.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' }));
                    } else {
                        $('#ticketDate').text('Bilinmiyor');
                        $('#ticketTime').text('Bilinmiyor');
                    }
                    
                    $('#ticketSeat').text(ticket.seat_number);
                    $('#ticketPrice').text(formatCurrency(ticket.price));
                    $('#ticketCustomer').text(ticket.user?.name || 'Bilinmiyor');
                    
                    $('#viewTicketModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Bilet bilgileri alınırken bir hata oluştu'
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
                    text: 'Bilet bilgileri alınırken bir hata oluştu'
                });
            }
        });
    }

    function editTicket(ticketId) {
        $.ajax({
            url: `${API_URL}/tickets/ticket-detail/${ticketId}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const ticket = response.data;
                    
                    $('#editTicketModal').modal('show');
                    
                    const ticketData = {
                        id: ticket.id,
                        showtime_id: ticket.showtime_id,
                        user_id: ticket.user_id,
                        seat_number: ticket.seat_number,
                        price: ticket.price,
                        status: ticket.status
                    };
                    
                    $('#edit_ticket_id').val(ticketData.id);
                    $('#edit_seat_number').val(ticketData.seat_number);
                    $('#edit_price').val(ticketData.price);
                    $('#edit_status').val(ticketData.status);
                    
                    fetchShowtimes(ticketData.showtime_id);
                    fetchUsers(ticketData.user_id);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Bilet bilgileri alınırken bir hata oluştu'
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
                    text: 'Bilet bilgileri alınırken bir hata oluştu'
                });
            }
        });
    }

    function saveTicket() {
        const formData = new FormData(addTicketForm[0]);
        const ticketData = {
            showtime_id: formData.get('showtime_id'),
            user_id: formData.get('user_id'),
            seat_number: formData.get('seat_number'),
            price: formData.get('price'),
            status: formData.get('status')
        };
        
        console.log('Gönderilen bilet verileri:', ticketData);
        
        $.ajax({
            url: TICKET_ADD_API,
            type: 'POST',
            data: ticketData,
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                console.log('Başarılı yanıt:', response);
                if (response.success) {
                    $('#addTicketModal').modal('hide');
                    addTicketForm[0].reset();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Bilet başarıyla eklendi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Bilet eklenirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                console.error('Hata yanıtı:', xhr.responseJSON);
                
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    console.error('Doğrulama hataları:', errors);
                    let errorMessage = '';
                    Object.keys(errors).forEach(key => {
                        errorMessage += errors[key][0] + '<br>';
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Doğrulama Hatası!',
                        html: errorMessage
                    });
                } else if (xhr.responseJSON?.message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Bilet eklenirken bir hata oluştu'
                    });
                }
            }
        });
    }

    function updateTicket() {
        const ticketId = $('#edit_ticket_id').val();
        const formData = new FormData(editTicketForm[0]);
        const ticketData = {
            showtime_id: formData.get('showtime_id'),
            user_id: formData.get('user_id'),
            seat_number: formData.get('seat_number'),
            price: formData.get('price'),
            status: formData.get('status')
        };
        
        console.log('Güncellenen bilet verileri:', ticketData);
        
        $.ajax({
            url: `${TICKET_UPDATE_API}/${ticketId}`,
            type: 'POST',
            data: ticketData,
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                console.log('Başarılı yanıt:', response);
                if (response.success) {
                    $('#editTicketModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Bilet başarıyla güncellendi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Bilet güncellenirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                console.error('Hata yanıtı:', xhr.responseJSON);
                
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
                
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    console.error('Doğrulama hataları:', errors);
                    let errorMessage = '';
                    Object.keys(errors).forEach(key => {
                        errorMessage += errors[key][0] + '<br>';
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Doğrulama Hatası!',
                        html: errorMessage
                    });
                } else if (xhr.responseJSON?.message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: xhr.responseJSON.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Bilet güncellenirken bir hata oluştu'
                    });
                }
            }
        });
    }

    function deleteTicket() {
        const ticketId = $('#delete_ticket_id').val();
        
        $.ajax({
            url: `${TICKET_DELETE_API}/${ticketId}`,
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteTicketModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Bilet başarıyla silindi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Bilet silinirken bir hata oluştu'
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
                    text: 'Bilet silinirken bir hata oluştu'
                });
            }
        });
    }
    
    function confirmDelete(ticketId, ticketCode) {
        $('#delete_ticket_id').val(ticketId);
        $('#deleteTicketName').text(ticketCode);
        $('#deleteTicketModal').modal('show');
    }
    
    function printTicket() {
        const ticketContent = document.getElementById('viewTicketModal').querySelector('.modal-body').innerHTML;
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Bilet Yazdır</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
                <style>
                    body {
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .ticket-header {
                        text-align: center;
                        margin-bottom: 20px;
                        padding-bottom: 10px;
                        border-bottom: 1px dashed #ccc;
                    }
                    .ticket-details {
                        margin-top: 20px;
                    }
                    .ticket-footer {
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
                <div class="ticket-header">
                    <h3>Sinema Bileti</h3>
                    <p>Sinema Otomasyonu</p>
                </div>
                ${ticketContent}
                <div class="ticket-footer">
                    <p>Bu bilet Sinema Otomasyonu tarafından oluşturulmuştur.</p>
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
            case 'confirmed':
                return 'success';
            case 'reserved':
                return 'warning';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    function getStatusText(status) {
        switch (status) {
            case 'confirmed':
                return 'Onaylandı';
            case 'reserved':
                return 'Rezerve Edildi';
            case 'cancelled':
                return 'İptal Edildi';
            default:
                return 'Bilinmiyor';
        }
    }
    
    function calculateStats(tickets) {
        let total = tickets.length;
        let confirmed = 0;
        let reserved = 0;
        let cancelled = 0;
        
        tickets.forEach(ticket => {
            if (ticket.status === 'confirmed') {
                confirmed++;
            } else if (ticket.status === 'reserved') {
                reserved++;
            } else if (ticket.status === 'cancelled') {
                cancelled++;
            }
        });
        
        $('#totalTickets').text(total);
        $('#completedTickets').text(confirmed);
        $('#pendingTickets').text(reserved);
        $('#cancelledTickets').text(cancelled);
    }
    
    function updateStats(stats) {
        if (stats) {
            $('#totalTickets').text(stats.total || 0);
            $('#completedTickets').text(stats.completed || 0);
            $('#pendingTickets').text(stats.pending || 0);
            $('#cancelledTickets').text(stats.cancelled || 0);
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
    
    fetchShowtimes();
    fetchUsers();

    $('#showtime_id').on('change', function() {
        const showtimeId = $(this).val();
        if (showtimeId) {
            fetchShowtimePrice(showtimeId);
        }
    });
    
    $('#edit_showtime_id').on('change', function() {
        const showtimeId = $(this).val();
        if (showtimeId) {
            fetchShowtimePrice(showtimeId, 'edit_');
        }
    });
    
    function fetchShowtimePrice(showtimeId, prefix = '') {
        const token = localStorage.getItem('token');
        
        $.ajax({
            url: `${API_URL}/showtimes/showtime-detail/${showtimeId}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const showtime = response.data;
                    $(`#${prefix}price`).val(showtime.price);
                }
            },
            error: function(xhr) {
                console.error('Seans fiyatı alınamadı:', xhr);
            }
        });
    }
}); 