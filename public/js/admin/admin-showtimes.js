$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    loadMovies();
    loadCinemas();
    
    const table = $('#showtimesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false, 
        ajax: {
            url: '/api/showtimes/datatable',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: function(d) {
                d.movie_id = $('#searchMovie').val();
                d.cinema_id = $('#searchCinema').val();
                d.date = $('#searchDate').val();
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
            { data: 'movie_title', name: 'movie.title', orderable: false },
            { data: 'cinema_hall', name: 'cinemaHall.name', orderable: false },
            { data: 'date', name: 'start_time', orderable: true },
            { data: 'time', name: 'start_time', orderable: true },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '/js/i18n/tr.json' 
        },
        drawCallback: function() {
            $('#showtimesTable tbody tr').addClass('animate__animated animate__fadeIn');
        }
    });
    
    setupModalManagement();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });
    
    $('#searchMovie, #searchCinema').on('change', function() {
        table.draw();
    });

    $('#searchDate').on('change', function() {
        table.draw();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchMovie').val('');
        $('#searchCinema').val('');
        $('#searchDate').val('');
        table.draw();
    });
    
    function fetchShowtimes() {
        $('#showtimesTable').DataTable().ajax.reload();
    }
    
    $('#cinema_id, #edit_cinema_id').on('change', function() {
        const cinemaId = $(this).val();
        console.log('Sinema seçildi, ID:', cinemaId);
        
        if (cinemaId) {
            const prefix = $(this).attr('id').startsWith('edit_') ? 'edit_' : '';
            loadHalls(cinemaId, prefix);
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

    $(document).on('click', '.edit-showtime', function() {
        const showtimeId = $(this).data('id');
        editShowtime(showtimeId);
    });

    $(document).on('click', '.delete-showtime', function() {
        const showtimeId = $(this).data('id');
        const showtimeName = $(this).data('name');
        confirmDelete(showtimeId, showtimeName);
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
        url: '/api/movies/all-movies',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response && response.success && response.data) {
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
            } else {
                console.error('Film verileri alınamadı:', response);
            }
        },
        error: function(xhr) {
            console.error('Film yükleme hatası:', xhr);
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
            if (response && response.success && response.data) {
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
            } else {
                console.error('Sinema verileri alınamadı:', response);
            }
        },
        error: function(xhr) {
            console.error('Sinema yükleme hatası:', xhr);
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
    
    if (!cinemaId) {
        console.error('Sinema ID boş, salonlar yüklenemedi');
        $(`#${prefix}hall_id`).html('<option value="">Önce Sinema Seçin</option>');
        return;
    }
    
    console.log(`Salonlar yükleniyor... Sinema ID: ${cinemaId}, Prefix: ${prefix}`);
    
    $.ajax({
        url: `/api/cinema-halls/hall-by-cinema/${cinemaId}`,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            console.log('Salon API yanıtı:', response);
            
            if (response && response.success && response.data) {
                const halls = Array.isArray(response.data) ? response.data : Object.values(response.data);
                let options = '<option value="">Salon Seçin</option>';
                
                if (halls && halls.length > 0) {
                    console.log(`${halls.length} salon bulundu`);
                    halls.forEach(hall => {
                        if (hall && hall.id && hall.name) {
                            options += `<option value="${hall.id}">${hall.name} (${hall.capacity || 0} Kişi)</option>`;
                        }
                    });
                } else {
                    console.log('Bu sinemada salon bulunamadı');
                }
                
                $(`#${prefix}hall_id`).html(options);
            } else {
                console.error('Salon verileri alınamadı:', response);
                $(`#${prefix}hall_id`).html('<option value="">Salon bulunamadı</option>');
            }
        },
        error: function(xhr) {
            console.error('Salon yükleme hatası:', xhr);
            
            if (xhr.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            
            $(`#${prefix}hall_id`).html('<option value="">Salon yüklenemedi</option>');
            
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Salonlar yüklenirken bir hata oluştu: ' + (xhr.responseJSON?.message || xhr.statusText)
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
            console.log('Seans detayı:', response);
            
            if (response.success && response.data) {
                const showtime = response.data;
                
                const startDateTime = new Date(showtime.start_time);
                const endDateTime = new Date(showtime.end_time);
                
                const date = startDateTime.toISOString().split('T')[0];
                
                const startTime = startDateTime.toTimeString().slice(0, 5);
                const endTime = endDateTime.toTimeString().slice(0, 5);
                
                console.log('Ayrıştırılmış tarih ve saat:', {
                    date: date,
                    startTime: startTime,
                    endTime: endTime
                });
                
                $('#editShowtimeId').val(showtime.id);
                $('#edit_movie_id').val(showtime.movie_id);
                
                if (showtime.cinema_hall && showtime.cinema_hall.cinema_id) {
                    $('#edit_cinema_id').val(showtime.cinema_hall.cinema_id);
                    loadHalls(showtime.cinema_hall.cinema_id, 'edit_');
                    
                    setTimeout(() => {
                        $('#edit_hall_id').val(showtime.cinema_hall_id);
                    }, 500);
                }
                
                // Tarih ve saat bilgilerini ayarla
                $('#edit_date').val(date);
                $('#edit_start_time').val(startTime);
                $('#edit_end_time').val(endTime);
                $('#edit_price').val(showtime.price);
                
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
    
    const movieId = $('#movie_id').val();
    const cinemaId = $('#cinema_id').val();
    const hallId = $('#hall_id').val();
    const date = $('#date').val();
    const startTime = $('#start_time').val();
    const endTime = $('#end_time').val();
    const price = $('#price').val();
    const isActive = $('#is_active').is(':checked');
    
    if (!movieId) {
        Swal.fire({
            icon: 'warning',
            title: 'Uyarı!',
            text: 'Lütfen bir film seçin'
        });
        return;
    }
    
    if (!cinemaId) {
        Swal.fire({
            icon: 'warning',
            title: 'Uyarı!',
            text: 'Lütfen bir sinema seçin'
        });
        return;
    }
    
    if (!hallId) {
        Swal.fire({
            icon: 'warning',
            title: 'Uyarı!',
            text: 'Lütfen bir salon seçin'
        });
        return;
    }
    
    if (!date || !startTime || !endTime) {
        Swal.fire({
            icon: 'warning',
            title: 'Uyarı!',
            text: 'Lütfen tarih, başlangıç ve bitiş saatlerini girin'
        });
        return;
    }
    
    if (startTime >= endTime) {
        Swal.fire({
            icon: 'warning',
            title: 'Uyarı!',
            text: 'Bitiş saati başlangıç saatinden sonra olmalıdır'
        });
        return;
    }
    
    const startDateTime = `${date} ${startTime}:00`;
    const endDateTime = `${date} ${endTime}:00`;
    
    console.log('Seans ekleniyor...', {
        movie_id: movieId,
        cinema_hall_id: hallId,
        start_time: startDateTime,
        end_time: endDateTime,
        price: price
    });
    
    const formData = {
        movie_id: movieId,
        cinema_hall_id: hallId,
        start_time: startDateTime,
        end_time: endDateTime,
        price: price
    };
    
    $.ajax({
        url: '/api/showtimes/showtime-add',
        type: 'POST',
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            console.log('Seans ekleme yanıtı:', response);
            
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
            console.error('Seans ekleme hatası:', xhr);
            
            if (xhr.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                console.error('Doğrulama hataları:', errors);
                Object.keys(errors).forEach(key => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Doğrulama Hatası!',
                        text: errors[key][0]
                    });
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: xhr.responseJSON?.message || 'Seans eklenirken bir hata oluştu'
                });
            }
        }
    });
}

function updateShowtime() {
    const token = localStorage.getItem('token');
    const id = $('#editShowtimeId').val();
    
    const movieId = $('#edit_movie_id').val();
    const hallId = $('#edit_hall_id').val();
    const date = $('#edit_date').val();
    const startTime = $('#edit_start_time').val();
    const endTime = $('#edit_end_time').val();
    const price = $('#edit_price').val();
    
    if (!movieId || !hallId || !date || !startTime || !endTime || !price) {
        Swal.fire({
            icon: 'warning',
            title: 'Uyarı!',
            text: 'Lütfen tüm alanları doldurun'
        });
        return;
    }
    
    if (startTime >= endTime) {
        Swal.fire({
            icon: 'warning',
            title: 'Uyarı!',
            text: 'Bitiş saati başlangıç saatinden sonra olmalıdır'
        });
        return;
    }
    
    const startDateTime = `${date} ${startTime}:00`;
    const endDateTime = `${date} ${endTime}:00`;
    
    console.log('Seans güncelleniyor...', {
        id: id,
        movie_id: movieId,
        cinema_hall_id: hallId,
        start_time: startDateTime,
        end_time: endDateTime,
        price: price
    });
    
    const formData = {
        movie_id: movieId,
        cinema_hall_id: hallId,
        start_time: startDateTime,
        end_time: endDateTime,
        price: price
    };
    
    $.ajax({
        url: `/api/showtimes/showtime-update/${id}`,
        type: 'POST',
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            console.log('Seans güncelleme yanıtı:', response);
            
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