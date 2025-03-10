
$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    loadMovies();
    
    loadYears();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        loadMovies();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        loadMovies();
    });
    
    $('#saveMovie').on('click', function() {
        const formData = new FormData($('#addMovieForm')[0]);
        
        $.ajax({
            url: '/api/movies/movie-add',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Authorization': 'Bearer ' + token
            },
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
                    loadMovies();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Film eklenirken bir hata oluştu'
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
                        text: 'Film eklenirken bir hata oluştu'
                    });
                }
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
            headers: {
                'Authorization': 'Bearer ' + token
            },
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
                    loadMovies();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Film güncellenirken bir hata oluştu'
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
                        text: 'Film güncellenirken bir hata oluştu'
                    });
                }
            }
        });
    });
    
    $('#confirmDeleteMovie').on('click', function() {
        const movieId = $('#deleteMovieId').val();
        
        $.ajax({
            url: `/api/movies/movie-delete/${movieId}`,
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
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
                    loadMovies();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'Film silinirken bir hata oluştu'
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
                    text: 'Film silinirken bir hata oluştu'
                });
            }
        });
    });
    
    $(document).on('click', '.edit-movie', function() {
        const movieId = $(this).data('id');
        
        $.ajax({
            url: `/api/movies/movie-detail/${movieId}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const movie = response.data;
                    
                    $('#editMovieId').val(movie.id);
                    $('#editTitle').val(movie.title);
                    $('#editGenre').val(movie.genre);
                    $('#editDuration').val(movie.duration);
                    
                    if (movie.release_date) {
                        const releaseDate = new Date(movie.release_date);
                        const formattedDate = releaseDate.toISOString().split('T')[0];
                        $('#editReleaseDate').val(formattedDate);
                    }
                    
                    $('#editIsInTheaters').val(movie.is_in_theaters ? 1 : 0);
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
                        text: 'Film bilgileri alınırken bir hata oluştu'
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
                    text: 'Film bilgileri alınırken bir hata oluştu'
                });
            }
        });
    });
    
    $(document).on('click', '.delete-movie', function() {
        const movieId = $(this).data('id');
        $('#deleteMovieId').val(movieId);
        $('#deleteMovieModal').modal('show');
    });
});

function loadMovies(page = 1) {
    const token = localStorage.getItem('token');
    const title = $('#searchTitle').val();
    const genre = $('#searchGenre').val();
    const year = $('#searchYear').val();
    
    $.ajax({
        url: '/api/movies/movie-list',
        type: 'GET',
        data: {
            page: page,
            title: title,
            genre: genre,
            release_year: year
        },
        headers: {
            'Authorization': 'Bearer ' + token
        },
        beforeSend: function() {
            $('#moviesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            if (response.success && response.data) {
                renderMovies(response.data.data);
                renderPagination(response.data);
            } else {
                $('#moviesTableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center">Veri yüklenirken bir hata oluştu</td>
                    </tr>
                `);
            }
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            
            $('#moviesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center">Veri yüklenirken bir hata oluştu</td>
                </tr>
            `);
        }
    });
}

function loadYears() {
    const currentYear = new Date().getFullYear();
    let options = '<option value="">Tümü</option>';
    
    for (let year = currentYear; year >= currentYear - 50; year--) {
        options += `<option value="${year}">${year}</option>`;
    }
    
    $('#searchYear').html(options);
}

function renderMovies(movies) {
    let html = '';
    
    if (movies.length === 0) {
        html = '<tr><td colspan="9" class="text-center">Hiç film bulunamadı</td></tr>';
    } else {
        movies.forEach(movie => {
            let releaseDate = 'Belirtilmemiş';
            if (movie.release_date) {
                const date = new Date(movie.release_date);
                releaseDate = date.toLocaleDateString('tr-TR');
            }
            
            html += `
            <tr class="animate__animated animate__fadeIn">
                <td>${movie.id}</td>
                <td>
                    ${movie.poster_url 
                        ? `<img src="${movie.poster_url}" width="50" height="75" alt="${movie.title}" class="img-thumbnail hover-zoom">` 
                        : `<div class="bg-light text-center" style="width:50px;height:75px;"><i class="fas fa-film mt-4"></i></div>`
                    }
                </td>
                <td>${movie.title}</td>
                <td>${movie.genre || '-'}</td>
                <td>${movie.duration} dk</td>
                <td>${releaseDate}</td>
                <td>${movie.imdb_rating ? `<span class="badge bg-warning text-dark">${movie.imdb_rating}</span>` : '-'}</td>
                <td>${movie.is_in_theaters 
                    ? '<span class="badge bg-gradient-success">Gösterimde</span>' 
                    : '<span class="badge bg-gradient-danger">Gösterimde Değil</span>'}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info edit-movie" data-id="${movie.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-movie" data-id="${movie.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;
        });
    }
    
    $('#moviesTableBody').html(html);
}

function renderPagination(data) {
    if (!data.last_page || data.last_page <= 1) {
        $('#pagination').html('');
        return;
    }
    
    let html = '<ul class="pagination justify-content-center">';
    
    html += `
        <li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadMovies(${data.current_page - 1})" aria-label="Önceki">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadMovies(${i})">${i}</a>
            </li>
        `;
    }
    
    html += `
        <li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadMovies(${data.current_page + 1})" aria-label="Sonraki">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    html += '</ul>';
    
    $('#pagination').html(html);
} 