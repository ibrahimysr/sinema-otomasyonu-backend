$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    loadYears();

    const table = $('#moviesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '/api/movies/datatable',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: function(d) {
                d.title = $('#searchTitle').val();
                d.genre = $('#searchGenre').val();
                d.release_year = $('#searchYear').val();
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Veriler yüklenirken bir hata oluştu'
                    });
                }
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'poster', name: 'poster', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'genre', name: 'genre' },
            { data: 'duration', name: 'duration' },
            { data: 'release_date', name: 'release_date' },
            { data: 'imdb_rating', name: 'imdb_rating' },
            { data: 'is_in_theaters', name: 'is_in_theaters', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/tr.json' // Türkçe dil dosyası
        },
        drawCallback: function() {
            $('#moviesTable tbody tr').addClass('animate__animated animate__fadeIn');
        }
    });

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        table.draw();
    });

    $('#refreshTable').on('click', function() {
        table.ajax.reload();
    });

    $('#saveMovie').on('click', function() {
        const formData = new FormData($('#addMovieForm')[0]);
        $.ajax({
            url: '/api/movies/movie-add',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                if (response.success) {
                    $('#addMovieModal').modal('hide');
                    $('#addMovieForm')[0].reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Film başarıyla eklendi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Film eklenirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr);
            }
        });
    });

    $(document).on('click', '.edit-movie', function() {
        const movieId = $(this).data('id');
        $.ajax({
            url: `/api/movies/movie-detail/${movieId}`,
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                if (response.success && response.data) {
                    const movie = response.data;
                    $('#editMovieId').val(movie.id);
                    $('#editTitle').val(movie.title);
                    $('#editGenre').val(movie.genre);
                    $('#editDuration').val(movie.duration);
                    $('#editReleaseDate').val(movie.release_date ? movie.release_date.split(' ')[0] : '');
                    $('#editIsInTheaters').val(movie.is_in_theaters ? '1' : '0');
                    $('#editLanguage').val(movie.language);
                    $('#editImdbRating').val(movie.imdb_rating);
                    $('#editDescription').val(movie.description);
                    $('#editPosterUrl').val(movie.poster_url);
                    $('#editImdbId').val(movie.imdb_id);

                    if (movie.poster_url) {
                        $('#currentPosterPreview').html(`<img src="${movie.poster_url}" alt="${movie.title}" class="img-thumbnail" style="max-height: 150px;">`);
                    } else {
                        $('#currentPosterPreview').html('');
                    }

                    $('#editMovieModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Film bilgileri alınamadı'
                    });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr);
            }
        });
    });

    $('#updateMovie').on('click', function() {
        const formData = new FormData($('#editMovieForm')[0]);
        const movieId = $('#editMovieId').val();
        $.ajax({
            url: `/api/movies/movie-update/${movieId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                if (response.success) {
                    $('#editMovieModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Film başarıyla güncellendi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Film güncellenirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr);
            }
        });
    });

    $(document).on('click', '.delete-movie', function() {
        const movieId = $(this).data('id');
        $('#deleteMovieId').val(movieId);
        $('#deleteMovieModal').modal('show');
    });

    $('#confirmDeleteMovie').on('click', function() {
        const movieId = $('#deleteMovieId').val();
        $.ajax({
            url: `/api/movies/movie-delete/${movieId}`,
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                if (response.success) {
                    $('#deleteMovieModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Film başarıyla silindi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Film silinirken bir hata oluştu'
                    });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr);
            }
        });
    });
});

function loadYears() {
    const currentYear = new Date().getFullYear();
    let options = '<option value="">Tümü</option>';
    for (let year = currentYear; year >= currentYear - 50; year--) {
        options += `<option value="${year}">${year}</option>`;
    }
    $('#searchYear').html(options);
}

function handleAjaxError(xhr) {
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
            text: 'Bir hata oluştu'
        });
    }
}