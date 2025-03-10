
$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    loadCinemas();
    
    fetchHalls();
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        fetchHalls();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchName').val('');
        $('#searchCinema').val('');
        $('#searchCapacity').val('');
        fetchHalls();
    });
    
    $('#saveHallBtn').on('click', function() {
        saveHall();
    });
    
    $('#updateHallBtn').on('click', function() {
        updateHall();
    });
    
    $('#confirmDeleteBtn').on('click', function() {
        deleteHall();
    });
});

function loadCinemas() {
    const token = localStorage.getItem('token');
    
    $.ajax({
        url: '/api/cinemas/cinema-list',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success && response.data) {
                const cinemas = response.data;
                let options = '<option value="">Tümü</option>';
                
                cinemas.forEach(cinema => {
                    options += `<option value="${cinema.id}">${cinema.name}</option>`;
                });
                
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

function fetchHalls() {
    const token = localStorage.getItem('token');
    const name = $('#searchName').val();
    const cinema = $('#searchCinema').val();
    const capacity = $('#searchCapacity').val();
    
    $.ajax({
        url: '/api/cinema-halls/hall-list',
        type: 'GET',
        data: {
            name: name,
            cinema_id: cinema,
            min_capacity: capacity
        },
        headers: {
            'Authorization': 'Bearer ' + token
        },
        beforeSend: function() {
            $('#hallsList').html(`
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            if (!response.success || !response.data) {
                $('#hallsList').html('<tr><td colspan="4" class="text-center">Veri yüklenirken bir hata oluştu</td></tr>');
                return;
            }
            
            const halls = response.data;
            
            if (halls.length === 0) {
                $('#hallsList').html('<tr><td colspan="4" class="text-center">Salon bulunamadı</td></tr>');
                return;
            }
            
            let html = '';
            
            halls.forEach(hall => {
                html += `
                    <tr class="animate__animated animate__fadeIn">
                        <td>${hall.id}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                    <i class="fas fa-door-open text-secondary"></i>
                                </div>
                                <div>
                                    <span class="d-block">${hall.name}</span>
                                    <small class="text-muted">${hall.cinema ? hall.cinema.name : '-'}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge ${getBadgeClass(hall.type)}">
                                <i class="fas fa-film me-1"></i>${hall.type || '-'}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-users me-1"></i>${hall.capacity} Kişi
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editHall(${hall.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${hall.id}, '${hall.name}')">
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
            
            $('#hallsList').html('<tr><td colspan="4" class="text-center">Veri yüklenirken bir hata oluştu</td></tr>');
        }
    });
}

function saveHall() {
    const token = localStorage.getItem('token');
    const formData = {
        name: $('#name').val(),
        cinema_id: $('#cinema_id').val(),
        capacity: $('#capacity').val(),
        type: $('#type').val()
    };
    
    $.ajax({
        url: '/api/cinema-halls/hall-add',
        type: 'POST',
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success) {
                $('#addHallModal').modal('hide');
                $('#addHallForm')[0].reset();
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Salon başarıyla eklendi',
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchHalls();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Salon eklenirken bir hata oluştu'
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
                    text: 'Salon eklenirken bir hata oluştu'
                });
            }
        }
    });
}

function editHall(id) {
    const token = localStorage.getItem('token');
    
    $.ajax({
        url: `/api/cinema-halls/hall-detail/${id}`,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success && response.data) {
                const hall = response.data;
                
                $('#editHallId').val(hall.id);
                $('#edit_name').val(hall.name);
                $('#edit_cinema_id').val(hall.cinema_id);
                $('#edit_capacity').val(hall.capacity);
                $('#edit_type').val(hall.type);
                
                $('#editHallModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Salon bilgileri alınırken bir hata oluştu'
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
                text: 'Salon bilgileri alınırken bir hata oluştu'
            });
        }
    });
}

function updateHall() {
    const token = localStorage.getItem('token');
    const id = $('#editHallId').val();
    const formData = {
        id: $('#editHallId').val(),
        name: $('#edit_name').val(),
        cinema_id: $('#edit_cinema_id').val(),
        capacity: $('#edit_capacity').val(),
        type: $('#edit_type').val()
    };
    
    $.ajax({
        url: `/api/cinema-halls/hall-update/${id}`,
        type: 'POST',
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success) {
                $('#editHallModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Salon başarıyla güncellendi',
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchHalls();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Salon güncellenirken bir hata oluştu'
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
                    text: 'Salon güncellenirken bir hata oluştu'
                });
            }
        }
    });
}

function confirmDelete(id, name) {
    $('#deleteHallId').val(id);
    $('#deleteHallName').text(name);
    $('#deleteHallModal').modal('show');
}

function deleteHall() {
    const token = localStorage.getItem('token');
    const id = $('#deleteHallId').val();
    
    $.ajax({
        url: `/api/cinema-halls/hall-delete/${id}`,
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            if (response.success) {
                $('#deleteHallModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Salon başarıyla silindi',
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchHalls();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message || 'Salon silinirken bir hata oluştu'
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
                text: 'Salon silinirken bir hata oluştu'
            });
        }
    });
}

function getBadgeClass(type) {
    switch(type) {
        case '2D':
            return 'bg-secondary';
        case '3D':
            return 'bg-primary';
        case 'IMAX':
            return 'bg-success';
        case '4DX':
            return 'bg-warning';
        default:
            return 'bg-secondary';
    }
} 