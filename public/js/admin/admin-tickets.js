
$(document).ready(function() {
    const API_URL = '/api';
    const TICKETS_API = `${API_URL}/tickets/ticket-list`;
    const TICKET_ADD_API = `${API_URL}/tickets/ticket-add`;
    const TICKET_UPDATE_API = `${API_URL}/tickets/ticket-update`;
    const TICKET_DELETE_API = `${API_URL}/tickets/ticket-delete`;
    const SHOWTIMES_API = `${API_URL}/showtimes/showtime-list`;
    const USERS_API = `${API_URL}/users/user-list`;
    const MOVIES_API = `${API_URL}/movies/movie-list`;

    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const ticketsTableBody = $('#ticketsTableBody');
    
    const addTicketForm = $('#addTicketForm');
    const editTicketForm = $('#editTicketForm');
    const saveTicketBtn = $('#saveTicketBtn');
    const updateTicketBtn = $('#updateTicketBtn');
    const confirmDeleteTicketBtn = $('#confirmDeleteTicketBtn');

    const showtimeSelect = $('#showtime_id');
    const userSelect = $('#user_id');
    const editShowtimeSelect = $('#edit_showtime_id');
    const editUserSelect = $('#edit_user_id');
    
    loadTickets();
    
    loadMovies();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        loadTickets();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        loadTickets();
    });
    
    $('#refreshTickets').on('click', function() {
        loadTickets();
    });
    
    $('#printTicketBtn').on('click', function() {
        printTicket();
    });
    
    function loadTickets() {
        ticketsTableBody.html(`
            <tr>
                <td colspan="11" class="text-center py-4">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="loading-spinner me-2"></div>
                        <span>Yükleniyor...</span>
                    </div>
                </td>
            </tr>
        `);
        
        const searchParams = {
            ticket_code: $('#searchTicketCode').val(),
            movie_id: $('#searchMovie').val(),
            date: $('#searchDate').val(),
            status: $('#searchStatus').val()
        };
        
        $.ajax({
            url: TICKETS_API,
            type: 'GET',
            data: searchParams,
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    displayTickets(response.data);
                    
                    if (response.stats) {
                        updateStats(response.stats);
                    } else {
                        calculateStats(response.data);
                    }
                } else {
                    ticketsTableBody.html(`
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Bilet bulunamadı
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
                
                ticketsTableBody.html(`
                    <tr>
                        <td colspan="11" class="text-center py-4">
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Biletler yüklenirken bir hata oluştu
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    function displayTickets(tickets) {
        ticketsTableBody.empty();
        
        tickets.forEach(ticket => {
            const statusClass = getStatusClass(ticket.status);
            const statusText = getStatusText(ticket.status);
            
            const row = $(`
                <tr class="animate__animated animate__fadeIn">
                    <td>${ticket.id}</td>
                    <td><span class="badge bg-secondary">${ticket.ticket_code || 'Bilinmiyor'}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="movie-poster me-2">
                                <img src="${ticket.showtime?.movie?.poster_url || '/img/no-poster.jpg'}" alt="${ticket.showtime?.movie?.title || 'Film'}" class="img-thumbnail" width="40">
                            </div>
                            <div>${ticket.showtime?.movie?.title || 'Bilinmiyor'}</div>
                        </div>
                    </td>
                    <td>${ticket.showtime?.cinema_hall?.cinema?.name || 'Bilinmiyor'}</td>
                    <td>${ticket.showtime?.cinema_hall?.name || 'Bilinmiyor'}</td>
                    <td>${formatDateTime(ticket.showtime?.start_time) || 'Bilinmiyor'}</td>
                    <td><span class="badge bg-info">${ticket.seat_number}</span></td>
                    <td>${ticket.user?.name || 'Bilinmiyor'}</td>
                    <td>${formatCurrency(ticket.price)}</td>
                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info view-ticket" data-id="${ticket.id}" title="Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary edit-ticket" data-id="${ticket.id}" title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-ticket" data-id="${ticket.id}" title="Sil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            
            ticketsTableBody.append(row);
        });
        
        $('.view-ticket').on('click', function() {
            const ticketId = $(this).data('id');
            viewTicket(ticketId);
        });
        
        $('.edit-ticket').on('click', function() {
            const ticketId = $(this).data('id');
            editTicket(ticketId);
        });
        
        $('.delete-ticket').on('click', function() {
            const ticketId = $(this).data('id');
            $('#delete_ticket_id').val(ticketId);
            $('#deleteTicketModal').modal('show');
        });
    }
    
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

    function fetchShowtimes() {
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
                populateShowtimeSelects(data.data);
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            showAlert('danger', 'Seanslar yüklenirken bir hata oluştu');
        });
    }

    function fetchUsers() {
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
                populateUserSelects(data.data);
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            showAlert('danger', 'Kullanıcılar yüklenirken bir hata oluştu');
        });
    }

    function populateShowtimeSelects(showtimes) {
        showtimeSelect.html('<option value="">Seans Seçin</option>');
        editShowtimeSelect.html('<option value="">Seans Seçin</option>');
        
        showtimes.forEach(showtime => {
            const option = `<option value="${showtime.id}">${showtime.movie?.title} - ${showtime.cinema_hall?.cinema?.name} - ${showtime.cinema_hall?.name} - ${formatDateTime(showtime.start_time)}</option>`;
            
            showtimeSelect.append(option);
            editShowtimeSelect.append(option);
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
    }

    saveTicketBtn.on('click', function() {
        const formData = new FormData(addTicketForm[0]);
        const ticketData = {
            showtime_id: formData.get('showtime_id'),
            user_id: formData.get('user_id'),
            seat_number: formData.get('seat_number'),
            price: formData.get('price'),
            status: formData.get('status')
        };
        
        $.ajax({
            url: TICKET_ADD_API,
            type: 'POST',
            data: JSON.stringify(ticketData),
            contentType: 'application/json',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
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
                    
                    loadTickets();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Bilet eklenirken bir hata oluştu'
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
                        text: 'Bilet eklenirken bir hata oluştu'
                    });
                }
            }
        });
    });

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
                    
                    $('#edit_ticket_id').val(ticket.id);
                    $('#edit_showtime_id').val(ticket.showtime_id);
                    $('#edit_user_id').val(ticket.user_id);
                    $('#edit_seat_number').val(ticket.seat_number);
                    $('#edit_price').val(ticket.price);
                    $('#edit_status').val(ticket.status);
                    
                    fetchShowtimes();
                    fetchUsers();
                    
                    $('#editTicketModal').modal('show');
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

    updateTicketBtn.on('click', function() {
        const ticketId = $('#edit_ticket_id').val();
        const formData = new FormData(editTicketForm[0]);
        const ticketData = {
            showtime_id: formData.get('showtime_id'),
            user_id: formData.get('user_id'),
            seat_number: formData.get('seat_number'),
            price: formData.get('price'),
            status: formData.get('status')
        };
        
        $.ajax({
            url: `${TICKET_UPDATE_API}/${ticketId}`,
            type: 'POST',
            data: JSON.stringify(ticketData),
            contentType: 'application/json',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success) {
                    $('#editTicketModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Bilet başarıyla güncellendi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    loadTickets();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Bilet güncellenirken bir hata oluştu'
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
                        text: 'Bilet güncellenirken bir hata oluştu'
                    });
                }
            }
        });
    });

    confirmDeleteTicketBtn.on('click', function() {
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
                    
                    loadTickets();
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
    });
    
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
    
    function calculateStats(tickets) {
        const stats = {
            total: tickets.length,
            completed: 0,
            pending: 0,
            cancelled: 0
        };
        
        tickets.forEach(ticket => {
            switch (ticket.status) {
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
}); 