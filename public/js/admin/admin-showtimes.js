
$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    loadMovies();
    loadCinemas();
    
    fetchShowtimes();
    
    
    setupModalManagement();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        fetchShowtimes();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchMovie').val('');
        $('#searchCinema').val('');
        $('#searchDate').val('');
        fetchShowtimes();
    });
    
    $('#cinema_id, #edit_cinema_id').on('change', function() {
        const cinemaId = $(this).val();
        if (cinemaId) {
            loadHalls(cinemaId, $(this).attr('id').startsWith('edit_') ? 'edit_' : '');
        } else {
            const prefix = $(this).attr('id').startsWith('edit_') ? 'edit_' : '';
            $(`#${prefix}hall_id`).html('<option value="">Önce Sinema Seçin</option>');
        }
    });
    
    $('#saveShowtimeBtn').on('click', function() {
        saveShowtime();
    });
    
    $('#updateShowtimeBtn').on('click', function() {
        updateShowtime();
    });
    
    $('#confirmDeleteBtn').on('click', function() {
        deleteShowtime();
    });
});

function setupModalManagement() {
    clearModalArtifacts();
    
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        clearModalArtifacts();
    });
    
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });
}

function clearModalArtifacts() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
}

function openModal(modalId) {
    $('.modal').modal('hide');
    clearModalArtifacts();
    $(`#${modalId}`).modal({
        backdrop: 'static',
        keyboard: true
    });
    $(`#${modalId}`).modal('show');
}

function closeModal(modalId) {
    $(`#${modalId}`).modal('hide');
    clearModalArtifacts();
}

function loadMovies() {
    const token = localStorage.getItem('token');
    
    $.ajax({
        url: '/api/movies/movie-list',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response && response.data) {
                const movies = Array.isArray(response.data) ? response.data : Object.values(response.data);
                let options = '<option value="">Tümü</option>';
                
                if (movies && movies.length > 0) {
                    movies.forEach(movie => {
                        if (movie && movie.id && movie.title) {
                            options += `<option value="${movie.id}">${movie.title}</option>`;
                        }
                    });
                }
                
                $('#searchMovie, #movie_id, #edit_movie_id').html(options);
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
                text: 'Filmler yüklenirken bir hata oluştu'
            });
        }
    });
}


let cinemaCache = {};

function loadCinemas() {
    const token = localStorage.getItem('token');
    
    $.ajax({
        url: '/api/cinemas/cinema-list',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response && response.data) {
                const cinemas = Array.isArray(response.data) ? response.data : Object.values(response.data);
                let options = '<option value="">Tümü</option>';
                
                if (cinemas && cinemas.length > 0) {
                    cinemas.forEach(cinema => {
                        if (cinema && cinema.id && cinema.name) {
                            cinemaCache[cinema.id] = cinema;
                            options += `<option value="${cinema.id}">${cinema.name}</option>`;
                        }
                    });
                }
                
                $('#searchCinema, #cinema_id, #edit_cinema_id').html(options);
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
                text: 'Sinemalar yüklenirken bir hata oluştu'
            });
        }
    });
}

function loadHalls(cinemaId, prefix = '') {
    const token = localStorage.getItem('token');
    
    $.ajax({
        url: `/api/cinema-halls/by-cinema/${cinemaId}`,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response && response.data) {
                const halls = Array.isArray(response.data) ? response.data : Object.values(response.data);
                let options = '<option value="">Salon Seçin</option>';
                
                if (halls && halls.length > 0) {
                    halls.forEach(hall => {
                        if (hall && hall.id && hall.name) {
                            options += `<option value="${hall.id}">${hall.name} (${hall.capacity || 0} Kişi)</option>`;
                        }
                    });
                }
                
                $(`#${prefix}hall_id`).html(options);
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
                text: 'Salonlar yüklenirken bir hata oluştu'
            });
        }
    });
}

async function getCinemaById(cinemaId) {
    const token = localStorage.getItem('token');
    
    try {
        const response = await $.ajax({
            url: `/api/cinemas/cinema-detail/${cinemaId}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });
        
        if (response.success && response.data) {
            return response.data;
        }
        return null;
    } catch (error) {
        console.error('Sinema bilgisi alınamadı:', error);
        return null;
    }
}

function fetchShowtimes() {
    const token = localStorage.getItem('token');
    const movie = $('#searchMovie').val();
    const cinema = $('#searchCinema').val();
    const date = $('#searchDate').val();
    
    $.ajax({
        url: '/api/showtimes/showtime-list',
        type: 'GET',
        data: {
            movie_id: movie,
            cinema_id: cinema,
            date: date
        },
        headers: {
            'Authorization': 'Bearer ' + token
        },
        beforeSend: function() {
            $('#showtimesList').html(`
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            console.log('API Yanıtı:', response);
            
            if (!response || !response.success) {
                $('#showtimesList').html('<tr><td colspan="7" class="text-center">Veri alınamadı</td></tr>');
                return;
            }

            let showtimes = Array.isArray(response.data) ? response.data : [];
            
            if (showtimes.length === 0) {
                $('#showtimesList').html('<tr><td colspan="7" class="text-center">Hiç seans bulunamadı</td></tr>');
                return;
            }
            
            let html = '';
            showtimes.forEach(showtime => {
                if (!showtime || !showtime.movie || !showtime.cinema_hall) return;
                
                const startTime = new Date(showtime.start_time);
                const date = startTime.toLocaleDateString('tr-TR');
                const time = startTime.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
                
                const movie = showtime.movie;
                const cinemaHall = showtime.cinema_hall;
                
                const cinema = cinemaCache[cinemaHall.cinema_id];
                
                html += `
                    <tr class="animate__animated animate__fadeIn">
                        <td>${showtime.id || ''}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                ${movie.poster_url ? 
                                    `<img src="${movie.poster_url}" class="me-2 rounded hover-zoom" width="40" height="40" alt="${movie.title || 'Film'}">` : 
                                    `<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="fas fa-film text-secondary"></i>
                                    </div>`
                                }
                                <div>
                                    <span class="d-block">${movie.title || 'İsimsiz Film'}</span>
                                    <small class="text-muted">${movie.duration || 0} dk</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="d-block">${cinema ? cinema.name : 'Bilinmeyen Sinema'}</span>
                                <small class="text-muted">${cinemaHall.name || 'Bilinmeyen Salon'} ${cinemaHall.type ? `(${cinemaHall.type})` : ''}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-calendar me-1"></i>${date}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                <i class="fas fa-clock me-1"></i>${time}
                            </span>
                        </td>
                        <td>
                            <span class="badge ${showtime.available_seats > 0 ? 'bg-success' : 'bg-danger'}">
                                ${showtime.available_seats} Koltuk
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editShowtime(${showtime.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${showtime.id}, '${movie.title || 'İsimsiz Film'} - ${date} ${time}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            if (html === '') {
                $('#showtimesList').html('<tr><td colspan="7" class="text-center">Hiç seans bulunamadı</td></tr>');
            } else {
                $('#showtimesList').html(html);
            }
        },
        error: function(xhr) {
            console.error('API Hatası:', xhr);
            
            if (xhr.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            
            let errorMessage = 'Seanslar yüklenirken bir hata oluştu';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            $('#showtimesList').html(`<tr><td colspan="7" class="text-center text-danger">
                <i class="fas fa-exclamation-circle me-2"></i>${errorMessage}</td></tr>`);
            
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: errorMessage
            });
        }
    });
}

function confirmDelete(id, name) {
    $('#deleteShowtimeId').val(id);
    $('#deleteShowtimeName').text(name);
    openModal('deleteShowtimeModal');
}

function editShowtime(id) {
    const token = localStorage.getItem('token');
    
    $.ajax({
        url: `/api/showtimes/showtime-detail/${id}`,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success && response.data) {
                const showtime = response.data;
                
                $('#editShowtimeId').val(showtime.id);
                $('#edit_movie_id').val(showtime.movie_id);
                $('#edit_cinema_id').val(showtime.cinema_id);
                loadHalls(showtime.cinema_id, 'edit_');
                setTimeout(() => {
                    $('#edit_hall_id').val(showtime.hall_id);
                }, 500);
                $('#edit_date').val(showtime.date);
                $('#edit_time').val(showtime.time);
                $('#edit_price').val(showtime.price);
                $('#edit_is_active').prop('checked', showtime.is_active);
                
                openModal('editShowtimeModal');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Seans bilgileri alınırken bir hata oluştu'
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
                text: 'Seans bilgileri alınırken bir hata oluştu'
            });
        }
    });
}

function saveShowtime() {
    const token = localStorage.getItem('token');
    const formData = {
        movie_id: $('#movie_id').val(),
        cinema_id: $('#cinema_id').val(),
        hall_id: $('#hall_id').val(),
        date: $('#date').val(),
        time: $('#time').val(),
        price: $('#price').val(),
        is_active: $('#is_active').is(':checked')
    };
    
    $.ajax({
        url: '/api/showtimes/showtime-add',
        type: 'POST',
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success) {
                closeModal('addShowtimeModal');
                $('#addShowtimeForm')[0].reset();
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Seans başarıyla eklendi',
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchShowtimes();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Seans eklenirken bir hata oluştu'
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
                    text: 'Seans eklenirken bir hata oluştu'
                });
            }
        }
    });
}

function updateShowtime() {
    const token = localStorage.getItem('token');
    const id = $('#editShowtimeId').val();
    const formData = {
        movie_id: $('#edit_movie_id').val(),
        cinema_id: $('#edit_cinema_id').val(),
        hall_id: $('#edit_hall_id').val(),
        date: $('#edit_date').val(),
        time: $('#edit_time').val(),
        price: $('#edit_price').val(),
        is_active: $('#edit_is_active').is(':checked')
    };
    
    $.ajax({
        url: `/api/showtimes/showtime-update/${id}`,
        type: 'POST',
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success) {
                closeModal('editShowtimeModal');
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Seans başarıyla güncellendi',
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchShowtimes();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Seans güncellenirken bir hata oluştu'
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
                    text: 'Seans güncellenirken bir hata oluştu'
                });
            }
        }
    });
}

function deleteShowtime() {
    const token = localStorage.getItem('token');
    const id = $('#deleteShowtimeId').val();
    
    $.ajax({
        url: `/api/showtimes/showtime-delete/${id}`,
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success) {
                closeModal('deleteShowtimeModal');
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Seans başarıyla silindi',
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchShowtimes();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Seans silinirken bir hata oluştu'
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
                text: 'Seans silinirken bir hata oluştu'
            });
        }
    });
} 