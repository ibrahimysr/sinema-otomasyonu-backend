$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    loadCities();

    const table = $('#cinemasTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false,
        ajax: {
            url: '/api/cinemas/datatable', 
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: function(d) {
                d.name = $('#searchName').val();
                d.city_id = $('#searchCity').val();
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
            { data: 'name', name: 'name' },
            { data: 'city', name: 'city.name' },
            { data: 'address', name: 'address' },
            { data: 'phone', name: 'phone' },
            { data: 'total_capacity', name: 'total_capacity' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '/js/i18n/tr.json' // Yerel Türkçe dil dosyası
        },
        drawCallback: function() {
            $('#cinemasTable tbody tr').addClass('animate__animated animate__fadeIn');
        }
    });

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#searchName').on('keyup', function() {
        table.draw();
    });

    $('#searchCity').on('change', function() {
        table.draw();
    });

    $('#resetSearch').on('click', function() {
        $('#searchForm')[0].reset();
        table.draw();
    });

    $('#refreshTable').on('click', function() {
        table.ajax.reload();
    });

    function fetchCinemas() {
        $('#cinemasTable').DataTable().ajax.reload();
    }

    $('#saveCinemaBtn').on('click', function() {
        saveCinema();
    });

    $('#updateCinemaBtn').on('click', function() {
        updateCinema();
    });

    // Sinema silme onayı
    $('#confirmDeleteBtn').on('click', function() {
        deleteCinema();
    });
});

function loadCities() {
    const token = localStorage.getItem('token');
    $.ajax({
        url: '/api/cities',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success && response.data) {
                const cities = response.data;
                let options = '<option value="">Tümü</option>';
                cities.forEach(city => {
                    options += `<option value="${city.id}">${city.name}</option>`;
                });
                $('#searchCity, #city, #edit_city').html(options);
            }
        },
        error: function(xhr) {
            handleAjaxError(xhr);
        }
    });
}

function saveCinema() {
    const token = localStorage.getItem('token');
    const formData = {
        name: $('#name').val(),
        city_id: $('#city').val(),
        address: $('#address').val(),
        latitude: $('#latitude').val() || null,
        longitude: $('#longitude').val() || null,
        total_capacity: parseInt($('#total_capacity').val()),
        phone: $('#phone').val(),
        description: $('#description').val()
    };

    $.ajax({
        url: '/api/cinemas/cinema-add',
        type: 'POST',
        data: formData,
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(response) {
            if (response.success) {
                $('#addCinemaModal').modal('hide');
                $('#addCinemaForm')[0].reset();
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Sinema başarıyla eklendi',
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#cinemasTable').DataTable().ajax.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Sinema eklenirken bir hata oluştu'
                });
            }
        },
        error: function(xhr) {
            handleAjaxError(xhr);
        }
    });
}

function editCinema(id) {
    const token = localStorage.getItem('token');
    $.ajax({
        url: `/api/cinemas/cinema-detail/${id}`,
        type: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(response) {
            if (response.success && response.data) {
                const cinema = response.data;
                $('#editCinemaId').val(cinema.id);
                $('#edit_name').val(cinema.name);
                $('#edit_city').val(cinema.city_id);
                $('#edit_address').val(cinema.address);
                $('#edit_latitude').val(cinema.latitude);
                $('#edit_longitude').val(cinema.longitude);
                $('#edit_total_capacity').val(cinema.total_capacity);
                $('#edit_phone').val(cinema.phone);
                $('#edit_description').val(cinema.description);
                $('#editCinemaModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Sinema bilgileri alınamadı'
                });
            }
        },
        error: function(xhr) {
            handleAjaxError(xhr);
        }
    });
}

$(document).on('click', '.edit-cinema', function() {
    const cinemaId = $(this).data('id');
    editCinema(cinemaId);
});

function updateCinema() {
    const token = localStorage.getItem('token');
    const id = $('#editCinemaId').val();
    const formData = {
        name: $('#edit_name').val(),
        city_id: $('#edit_city').val(),
        address: $('#edit_address').val(),
        latitude: $('#edit_latitude').val() || null,
        longitude: $('#edit_longitude').val() || null,
        total_capacity: parseInt($('#edit_total_capacity').val()),
        phone: $('#edit_phone').val(),
        description: $('#edit_description').val()
    };

    $.ajax({
        url: `/api/cinemas/cinema-update/${id}`,
        type: 'POST',
        data: formData,
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(response) {
            if (response.success) {
                $('#editCinemaModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Sinema başarıyla güncellendi',
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#cinemasTable').DataTable().ajax.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Sinema güncellenirken bir hata oluştu'
                });
            }
        },
        error: function(xhr) {
            handleAjaxError(xhr);
        }
    });
}

$(document).on('click', '.delete-cinema', function() {
    const cinemaId = $(this).data('id');
    const cinemaName = $(this).data('name');
    $('#deleteCinemaId').val(cinemaId);
    $('#deleteCinemaName').text(cinemaName);
    $('#deleteCinemaModal').modal('show');
});

function deleteCinema() {
    const token = localStorage.getItem('token');
    const id = $('#deleteCinemaId').val();
    $.ajax({
        url: `/api/cinemas/cinema-delete/${id}`,
        type: 'POST',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(response) {
            if (response.success) {
                $('#deleteCinemaModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Sinema başarıyla silindi',
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#cinemasTable').DataTable().ajax.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Sinema silinirken bir hata oluştu'
                });
            }
        },
        error: function(xhr) {
            handleAjaxError(xhr);
        }
    });
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
            text: 'Bir hata oluştu: ' + xhr.statusText
        });
    }
}