@extends('admin.layouts.app')

@section('title', 'Sinema Salonları')

@section('actions')
<button type="button" class="btn btn-primary animate__animated animate__fadeInRight" data-bs-toggle="modal" data-bs-target="#addHallModal">
    <i class="fas fa-plus"></i> Yeni Salon Ekle
</button>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card dashboard-card mb-4 animate__animated animate__fadeInDown">
        <div class="card-body">
            <form id="searchForm" class="row g-3">
                <div class="col-md-4">
                    <label for="searchName" class="form-label">Salon Adı</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                    <input type="text" class="form-control" id="searchName" placeholder="Salon adı ara...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="searchCinema" class="form-label">Sinema</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <select class="form-select" id="searchCinema">
                        <option value="">Tümü</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="searchCapacity" class="form-label">Min. Kapasite</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                    <input type="number" class="form-control" id="searchCapacity" min="1" placeholder="Min. kapasite">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary w-100" id="resetSearch">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card dashboard-card animate__animated animate__fadeInUp animate__delay-1s">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-door-open me-2 text-primary"></i>
                Salon Listesi
            </h5>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-secondary" onclick="fetchHalls()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="hallsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>ID</th>
                            <th><i class="fas fa-door-open me-2"></i>Salon</th>
                            <th><i class="fas fa-building me-2"></i>Sinema</th>
                            <th><i class="fas fa-film me-2"></i>Tip</th>
                            <th><i class="fas fa-users me-2"></i>Kapasite</th>
                            <th><i class="fas fa-cog me-2"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="hallsList">
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="loading-spinner me-2"></div>
                                    <span>Yükleniyor...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addHallModal" tabindex="-1" aria-labelledby="addHallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHallModalLabel">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Yeni Salon Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="addHallForm">
                    <div class="mb-3">
                            <label for="name" class="form-label">Salon Adı</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                            <label for="cinema_id" class="form-label">Sinema</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <select class="form-select" id="cinema_id" name="cinema_id" required>
                                <option value="">Sinema Seçin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                            <label for="capacity" class="form-label">Kapasite</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                            <label for="type" class="form-label">Salon Tipi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-film"></i></span>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Salon Tipi Seçin</option>
                                <option value="2D">2D</option>
                                <option value="3D">3D</option>
                                <option value="IMAX">IMAX</option>
                                <option value="4DX">4DX</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveHallBtn">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editHallModal" tabindex="-1" aria-labelledby="editHallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHallModalLabel">
                    <i class="fas fa-edit me-2 text-info"></i>
                    Salon Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="editHallForm">
                    <input type="hidden" id="editHallId" name="id">
                    <div class="mb-3">
                            <label for="edit_name" class="form-label">Salon Adı</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                            <label for="edit_cinema_id" class="form-label">Sinema</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <select class="form-select" id="edit_cinema_id" name="cinema_id" required>
                                <option value="">Sinema Seçin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                            <label for="edit_capacity" class="form-label">Kapasite</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                            <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                            <label for="edit_type" class="form-label">Salon Tipi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-film"></i></span>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="">Salon Tipi Seçin</option>
                                <option value="2D">2D</option>
                                <option value="3D">3D</option>
                                <option value="IMAX">IMAX</option>
                                <option value="4DX">4DX</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="updateHallBtn">
                    <i class="fas fa-save me-2"></i>Güncelle
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteHallModal" tabindex="-1" aria-labelledby="deleteHallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteHallModalLabel">
                    <i class="fas fa-trash-alt me-2 text-danger"></i>
                    Salon Silme Onayı
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Bu salonu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                </div>
                <p><strong id="deleteHallName"></strong></p>
                <input type="hidden" id="deleteHallId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i>Evet, Sil
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seatLayoutModal" tabindex="-1" aria-labelledby="seatLayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seatLayoutModalLabel">Koltuk Düzeni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 id="seatLayoutTitle"></h6>
                    <button type="button" class="btn btn-sm btn-primary" id="addSeatBtn">
                        <i class="fas fa-plus"></i> Yeni Koltuk Ekle
                    </button>
                </div>
                <div class="seat-layout-container p-3 border rounded bg-light">
                    <div class="text-center mb-4">
                        <div class="bg-dark text-white p-2 rounded">PERDE</div>
                    </div>
                    <div id="seatLayout" class="d-flex flex-wrap justify-content-center">
                        <div class="text-center p-3">Yükleniyor...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="/js/admin/admin-cinema-halls.js"></script>
@endsection 