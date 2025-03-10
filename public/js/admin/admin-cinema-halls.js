$(document).ready(function() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    loadCinemas();
    
    const table = $('#hallsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false, 
        ajax: {
            url: '/api/cinema-halls/datatable',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: function(d) {
                d.name = $('#searchName').val();
                d.cinema_id = $('#searchCinema').val();
                d.min_capacity = $('#searchCapacity').val();
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
            { data: 'cinema_name', name: 'cinema.name', orderable: false },
            { data: 'type', name: 'type', orderable: false },
            { data: 'capacity', name: 'capacity', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '/js/i18n/tr.json' 
        },
        drawCallback: function() {
            $('#hallsTable tbody tr').addClass('animate__animated animate__fadeIn');
        }
    });
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });
    
    $('#searchName').on('keyup', function() {
        table.draw();
    });

    $('#searchCinema').on('change', function() {
        table.draw();
    });

    $('#searchCapacity').on('change', function() {
        table.draw();
    });
    
    $('#resetSearch').on('click', function() {
        $('#searchName').val('');
        $('#searchCinema').val('');
        $('#searchCapacity').val('');
        table.draw();
    });
    
    function fetchHalls() {
        $('#hallsTable').DataTable().ajax.reload();
    }
    
    $('#saveHallBtn').on('click', function() {
        saveHall();
    });
    
    $('#updateHallBtn').on('click', function() {
        updateHall();
    });
    
    $('#confirmDeleteBtn').on('click', function() {
        deleteHall();
    });

    $(document).on('click', '.edit-hall', function() {
        const hallId = $(this).data('id');
        editHall(hallId);
    });

    $(document).on('click', '.delete-hall', function() {
        const hallId = $(this).data('id');
        const hallName = $(this).data('name');
        confirmDelete(hallId, hallName);
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