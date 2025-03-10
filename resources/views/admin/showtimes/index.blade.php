@extends('admin.layouts.app')

@section('title', 'Seanslar')

@section('actions')
<button type="button" class="btn btn-primary animate__animated animate__fadeInRight" data-bs-toggle="modal" data-bs-target="#addShowtimeModal">
    <i class="fas fa-plus"></i> Yeni Seans Ekle
</button>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card dashboard-card mb-4 animate__animated animate__fadeInDown">
        <div class="card-body">
            <form id="searchForm" class="row g-3">
                <div class="col-md-4">
                    <label for="searchMovie" class="form-label">Film</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-film"></i></span>
                    <select class="form-select" id="searchMovie">
                        <option value="">Tümü</option>
                    </select>
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
                    <label for="searchDate" class="form-label">Tarih</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <input type="date" class="form-control" id="searchDate">
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
                <i class="fas fa-clock me-2 text-primary"></i>
                Seans Listesi
            </h5>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-secondary" onclick="fetchShowtimes()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>ID</th>
                            <th><i class="fas fa-film me-2"></i>Film</th>
                            <th><i class="fas fa-building me-2"></i>Sinema/Salon</th>
                            <th><i class="fas fa-calendar me-2"></i>Tarih</th>
                            <th><i class="fas fa-clock me-2"></i>Saat</th>
                            <th><i class="fas fa-check-circle me-2"></i>Durum</th>
                            <th><i class="fas fa-cog me-2"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="showtimesList">
                        <tr>
                            <td colspan="7" class="text-center py-4">
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

<div class="modal fade" id="addShowtimeModal" tabindex="-1" aria-labelledby="addShowtimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShowtimeModalLabel">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Yeni Seans Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="addShowtimeForm">
                    <div class="mb-3">
                            <label for="movie_id" class="form-label">Film</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-film"></i></span>
                            <select class="form-select" id="movie_id" name="movie_id" required>
                                <option value="">Film Seçin</option>
                            </select>
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
                            <label for="hall_id" class="form-label">Salon</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                            <select class="form-select" id="hall_id" name="hall_id" required>
                                <option value="">Önce Sinema Seçin</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Tarih</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="time" class="form-label">Saat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="time" class="form-control" id="time" name="time" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Bilet Fiyatı</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lira-sign"></i></span>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveShowtimeBtn">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editShowtimeModal" tabindex="-1" aria-labelledby="editShowtimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editShowtimeModalLabel">
                    <i class="fas fa-edit me-2 text-info"></i>
                    Seans Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="editShowtimeForm">
                    <input type="hidden" id="editShowtimeId" name="id">
                    <div class="mb-3">
                            <label for="edit_movie_id" class="form-label">Film</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-film"></i></span>
                            <select class="form-select" id="edit_movie_id" name="movie_id" required>
                                <option value="">Film Seçin</option>
                            </select>
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
                            <label for="edit_hall_id" class="form-label">Salon</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                            <select class="form-select" id="edit_hall_id" name="hall_id" required>
                                <option value="">Önce Sinema Seçin</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_date" class="form-label">Tarih</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_time" class="form-label">Saat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="time" class="form-control" id="edit_time" name="time" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Bilet Fiyatı</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lira-sign"></i></span>
                            <input type="number" class="form-control" id="edit_price" name="price" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">Aktif</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="updateShowtimeBtn">
                    <i class="fas fa-save me-2"></i>Güncelle
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteShowtimeModal" tabindex="-1" aria-labelledby="deleteShowtimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteShowtimeModalLabel">
                    <i class="fas fa-trash-alt me-2 text-danger"></i>
                    Seans Silme Onayı
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Bu seansı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                </div>
                <p><strong id="deleteShowtimeName"></strong></p>
                <input type="hidden" id="deleteShowtimeId">
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

@endsection

@section('scripts')
<script src="/js/admin/admin-showtimes.js"></script>
@endsection 