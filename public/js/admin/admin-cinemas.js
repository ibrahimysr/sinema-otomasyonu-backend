
$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    loadCities();
    
    fetchCinemas();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        fetchCinemas();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchName').val('');
        $('#searchCity').val('');
        fetchCinemas();
    });
    
    $('#saveCinemaBtn').on('click', function() {
        saveCinema();
    });
    
    $('#updateCinemaBtn').on('click', function() {
        updateCinema();
    });
    
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
            if (xhr.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Şehirler yüklenirken bir hata oluştu'
            });
        }
    });
}

function fetchCinemas() {
    const token = localStorage.getItem('token');
    const name = $('#searchName').val();
    const city = $('#searchCity').val();
    
    $.ajax({
        url: '/api/cinemas/cinema-list',
        type: 'GET',
        data: {
            name: name,
            city_id: city
        },
        headers: {
            'Authorization': 'Bearer ' + token
        },
        beforeSend: function() {
            $('#cinemasList').html(`
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            if (!response.success || !response.data) {
                $('#cinemasList').html('<tr><td colspan="6" class="text-center">Veri yüklenirken bir hata oluştu</td></tr>');
                return;
            }
            
            const cinemas = response.data;
            
            if (cinemas.length === 0) {
                $('#cinemasList').html('<tr><td colspan="6" class="text-center">Sinema bulunamadı</td></tr>');
                return;
            }
            
            let html = '';
            
            cinemas.forEach(cinema => {
                html += `
                    <tr class="animate__animated animate__fadeIn">
                        <td>${cinema.id}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                ${cinema.image ? 
                                    `<img src="${cinema.image}" class="me-2 rounded-circle hover-zoom" width="40" height="40" alt="${cinema.name}">` : 
                                    `<div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="fas fa-building text-secondary"></i>
                                    </div>`
                                }
                                <span>${cinema.name}</span>
                            </div>
                        </td>
                        <td>${cinema.city ? cinema.city.name : '-'}</td>
                        <td>${cinema.address || '-'}</td>
                        <td>${cinema.phone || '-'}</td>
                        <td>${cinema.total_capacity || '0'}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editCinema(${cinema.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${cinema.id}, '${cinema.name.replace(/'/g, "\\'")}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            $('#cinemasList').html(html);
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            
            $('#cinemasList').html('<tr><td colspan="6" class="text-center">Veri yüklenirken bir hata oluştu</td></tr>');
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
        headers: {
            'Authorization': 'Bearer ' + token
        },
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
                fetchCinemas();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Sinema eklenirken bir hata oluştu'
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
                    text: 'Sinema eklenirken bir hata oluştu'
                });
            }
        }
    });
}

function editCinema(id) {
    const token = localStorage.getItem('token');
    
    $.ajax({
        url: `/api/cinemas/cinema-detail/${id}`,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
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
                    text: 'Sinema bilgileri alınırken bir hata oluştu'
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
                text: 'Sinema bilgileri alınırken bir hata oluştu'
            });
        }
    });
}

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
        headers: {
            'Authorization': 'Bearer ' + token
        },
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
                fetchCinemas();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Sinema güncellenirken bir hata oluştu'
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
                    text: 'Sinema güncellenirken bir hata oluştu'
                });
            }
        }
    });
}

function confirmDelete(id, name) {
    $('#deleteCinemaId').val(id);
    $('#deleteCinemaName').text(name);
    $('#deleteCinemaModal').modal('show');
}

function deleteCinema() {
    const token = localStorage.getItem('token');
    const id = $('#deleteCinemaId').val();
    
    $.ajax({
        url: `/api/cinemas/cinema-delete/${id}`,
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token
        },
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
                fetchCinemas();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Sinema silinirken bir hata oluştu'
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
                text: 'Sinema silinirken bir hata oluştu'
            });
        }
    });
}

function viewCinemaHalls(cinemaId, cinemaName) {
    const token = localStorage.getItem('token');
    
    $('#cinemaHallsTitle').text(`${cinemaName} - Salonlar`);
    $('#cinemaHallsModal').modal('show');
    
    $.ajax({
        url: `/api/cinema-halls/by-cinema/${cinemaId}`,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        beforeSend: function() {
            $('#hallsList').html(`
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            if (!response.success || !response.data) {
                $('#hallsList').html('<tr><td colspan="5" class="text-center">Veri yüklenirken bir hata oluştu</td></tr>');
                return;
            }
            
            const halls = response.data;
            
            if (halls.length === 0) {
                $('#hallsList').html('<tr><td colspan="5" class="text-center">Bu sinemaya ait salon bulunamadı</td></tr>');
                return;
            }
            
            let html = '';
            halls.forEach(hall => {
                html += `
                    <tr class="animate__animated animate__fadeIn">
                        <td>${hall.id}</td>
                        <td>${hall.name}</td>
                        <td>${hall.capacity}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editHall(${hall.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeleteHall(${hall.id}, '${hall.name}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            $('#hallsList').html(html);
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            
            $('#hallsList').html('<tr><td colspan="5" class="text-center">Veri yüklenirken bir hata oluştu</td></tr>');
        }
    });
} 