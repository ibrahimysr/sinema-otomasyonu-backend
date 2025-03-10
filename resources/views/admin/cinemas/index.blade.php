@extends('admin.layouts.app')

@section('title', 'Sinemalar')

@section('actions')
<button type="button" class="btn btn-primary animate__animated animate__fadeInRight" data-bs-toggle="modal" data-bs-target="#addCinemaModal">
    <i class="fas fa-plus"></i> Yeni Sinema Ekle
</button>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card dashboard-card mb-4 animate__animated animate__fadeInDown">
        <div class="card-body">
            <form id="searchForm" class="row g-3">
                <div class="col-md-5">
                    <label for="searchName" class="form-label">Sinema Adı</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input type="text" class="form-control" id="searchName" placeholder="Sinema adı ara...">
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="searchCity" class="form-label">Şehir</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <select class="form-select" id="searchCity">
                        <option value="">Tümü</option>
                        <!-- Şehirler API'den yüklenecek -->
                    </select>
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
                <i class="fas fa-building me-2 text-primary"></i>
                Sinema Listesi
            </h5>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-secondary" onclick="fetchCinemas()">
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
                            <th><i class="fas fa-building me-2"></i>Sinema</th>
                            <th><i class="fas fa-map-marker-alt me-2"></i>Şehir</th>
                            <th><i class="fas fa-map me-2"></i>Adres</th>
                            <th><i class="fas fa-phone me-2"></i>Telefon</th>
                            <th><i class="fas fa-users me-2"></i>Toplam Kapasite</th>
                            <th><i class="fas fa-cog me-2"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="cinemasList">
                        <tr>
                            <td colspan="6" class="text-center py-4">
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

<div class="modal fade" id="addCinemaModal" tabindex="-1" aria-labelledby="addCinemaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCinemaModalLabel">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Yeni Sinema Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="addCinemaForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Sinema Adı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label">Şehir</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <select class="form-select" id="city" name="city_id" required>
                                    <option value="">Şehir Seçin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Adres</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Enlem</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                <input type="number" step="0.0001" class="form-control" id="latitude" name="latitude" placeholder="Örn: 41.0669">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Boylam</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                <input type="number" step="0.0001" class="form-control" id="longitude" name="longitude" placeholder="Örn: 29.0169">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="total_capacity" class="form-label">Toplam Kapasite</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                <input type="number" class="form-control" id="total_capacity" name="total_capacity" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telefon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="0212 999 88 77">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveCinemaBtn">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editCinemaModal" tabindex="-1" aria-labelledby="editCinemaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCinemaModalLabel">
                    <i class="fas fa-edit me-2 text-info"></i>
                    Sinema Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="editCinemaForm">
                    <input type="hidden" id="editCinemaId" name="id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Sinema Adı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_city" class="form-label">Şehir</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <select class="form-select" id="edit_city" name="city_id" required>
                                    <option value="">Şehir Seçin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Adres</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                            <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_latitude" class="form-label">Enlem</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                <input type="number" step="0.0001" class="form-control" id="edit_latitude" name="latitude" placeholder="Örn: 41.0669">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_longitude" class="form-label">Boylam</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                <input type="number" step="0.0001" class="form-control" id="edit_longitude" name="longitude" placeholder="Örn: 29.0169">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_total_capacity" class="form-label">Toplam Kapasite</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                <input type="number" class="form-control" id="edit_total_capacity" name="total_capacity" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">Telefon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="edit_phone" name="phone" placeholder="0212 999 88 77">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Açıklama</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="updateCinemaBtn">
                    <i class="fas fa-save me-2"></i>Güncelle
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCinemaModal" tabindex="-1" aria-labelledby="deleteCinemaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCinemaModalLabel">
                    <i class="fas fa-trash-alt me-2 text-danger"></i>
                    Sinema Silme Onayı
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Bu sinemayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                </div>
                <p><strong id="deleteCinemaName"></strong></p>
                <input type="hidden" id="deleteCinemaId">
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

<div class="modal fade" id="cinemaHallsModal" tabindex="-1" aria-labelledby="cinemaHallsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cinemaHallsModalLabel">
                    <i class="fas fa-door-open me-2 text-info"></i>
                    Sinema Salonları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 id="cinemaHallsTitle" class="mb-0"></h6>
                    <button type="button" class="btn btn-sm btn-primary" id="addHallBtn">
                        <i class="fas fa-plus me-2"></i>Yeni Salon Ekle
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>ID</th>
                                <th><i class="fas fa-door-open me-2"></i>Salon Adı</th>
                                <th><i class="fas fa-users me-2"></i>Kapasite</th>
                                <th><i class="fas fa-check-circle me-2"></i>Durum</th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Kapat
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="/js/admin/admin-cinemas.js"></script>
@endsection 