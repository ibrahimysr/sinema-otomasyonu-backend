@extends('admin.layouts.app')

@section('title', 'Filmler')

@section('actions')
<button type="button" class="btn btn-primary animate__animated animate__fadeInRight" data-bs-toggle="modal" data-bs-target="#addMovieModal">
    <i class="fas fa-plus"></i> Yeni Film Ekle
</button>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card dashboard-card mb-4 animate__animated animate__fadeInDown">
        <div class="card-body">
            <form id="searchForm" class="row g-3">
                <div class="col-md-3">
                    <label for="searchTitle" class="form-label">Film Adı</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-film"></i></span>
                    <input type="text" class="form-control" id="searchTitle" placeholder="Film adı ara...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="searchGenre" class="form-label">Tür</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-theater-masks"></i></span>
                    <select class="form-select" id="searchGenre">
                        <option value="">Tümü</option>
                        <option value="Action">Aksiyon</option>
                        <option value="Adventure">Macera</option>
                        <option value="Comedy">Komedi</option>
                        <option value="Drama">Drama</option>
                        <option value="Sci-Fi">Bilim Kurgu</option>
                        <option value="Horror">Korku</option>
                        <option value="Romance">Romantik</option>
                        <option value="Animation">Animasyon</option>
                        <option value="Documentary">Belgesel</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="searchYear" class="form-label">Yıl</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <select class="form-select" id="searchYear">
                        <option value="">Tümü</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Ara
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary w-100" id="resetSearch">
                        <i class="fas fa-redo me-2"></i> Sıfırla
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card dashboard-card animate__animated animate__fadeInUp animate__delay-1s">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-film me-2 text-primary"></i>
                Film Listesi
            </h5>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-secondary" id="refreshMovies">
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
                            <th><i class="fas fa-image me-2"></i>Afiş</th>
                            <th><i class="fas fa-film me-2"></i>Film Adı</th>
                            <th><i class="fas fa-theater-masks me-2"></i>Tür</th>
                            <th><i class="fas fa-clock me-2"></i>Süre</th>
                            <th><i class="fas fa-calendar me-2"></i>Yayın Tarihi</th>
                            <th><i class="fas fa-star me-2"></i>IMDB</th>
                            <th><i class="fas fa-check-circle me-2"></i>Durum</th>
                            <th><i class="fas fa-cog me-2"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="moviesTableBody">
                        <!-- Filmler API'den yüklenecek -->
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="loading-spinner me-2"></div>
                                    <span>Yükleniyor...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="mt-3">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMovieModal" tabindex="-1" aria-labelledby="addMovieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMovieModalLabel">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Yeni Film Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="addMovieForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Film Adı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-film"></i></span>
                            <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="genre" class="form-label">Tür</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-theater-masks"></i></span>
                            <input type="text" class="form-control" id="genre" name="genre" placeholder="Örn: Action, Adventure, Sci-Fi" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="duration" class="form-label">Süre (dakika)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="number" class="form-control" id="duration" name="duration" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="release_date" class="form-label">Yayın Tarihi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" class="form-control" id="release_date" name="release_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="is_in_theaters" class="form-label">Gösterimde mi?</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <select class="form-select" id="is_in_theaters" name="is_in_theaters" required>
                                <option value="1">Evet</option>
                                <option value="0">Hayır</option>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="language" class="form-label">Dil</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-language"></i></span>
                            <input type="text" class="form-control" id="language" name="language" placeholder="Örn: English, Turkish">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="imdb_rating" class="form-label">IMDB Puanı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                            <input type="number" step="0.1" min="0" max="10" class="form-control" id="imdb_rating" name="imdb_rating">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Film Açıklaması</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="poster_url" class="form-label">Afiş URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                        <input type="url" class="form-control" id="poster_url" name="poster_url" placeholder="https://...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="imdb_id" class="form-label">IMDB ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-imdb"></i></span>
                        <input type="text" class="form-control" id="imdb_id" name="imdb_id" placeholder="tt1375666">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveMovie">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editMovieModal" tabindex="-1" aria-labelledby="editMovieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMovieModalLabel">
                    <i class="fas fa-edit me-2 text-info"></i>
                    Film Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="editMovieForm">
                    <input type="hidden" id="editMovieId" name="id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editTitle" class="form-label">Film Adı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-film"></i></span>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editGenre" class="form-label">Tür</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-theater-masks"></i></span>
                            <input type="text" class="form-control" id="editGenre" name="genre" placeholder="Örn: Action, Adventure, Sci-Fi" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editDuration" class="form-label">Süre (dakika)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="number" class="form-control" id="editDuration" name="duration" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="editReleaseDate" class="form-label">Yayın Tarihi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" class="form-control" id="editReleaseDate" name="release_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="editIsInTheaters" class="form-label">Gösterimde mi?</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <select class="form-select" id="editIsInTheaters" name="is_in_theaters" required>
                                <option value="1">Evet</option>
                                <option value="0">Hayır</option>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editLanguage" class="form-label">Dil</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-language"></i></span>
                            <input type="text" class="form-control" id="editLanguage" name="language" placeholder="Örn: English, Turkish">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editImdbRating" class="form-label">IMDB Puanı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                            <input type="number" step="0.1" min="0" max="10" class="form-control" id="editImdbRating" name="imdb_rating">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Film Açıklaması</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editPosterUrl" class="form-label">Afiş URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                        <input type="url" class="form-control" id="editPosterUrl" name="poster_url" placeholder="https://...">
                        </div>
                        <div class="mt-2" id="currentPosterPreview"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editImdbId" class="form-label">IMDB ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-imdb"></i></span>
                        <input type="text" class="form-control" id="editImdbId" name="imdb_id" placeholder="tt1375666">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="updateMovie">
                    <i class="fas fa-save me-2"></i>Güncelle
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteMovieModal" tabindex="-1" aria-labelledby="deleteMovieModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMovieModalLabel">
                    <i class="fas fa-trash-alt me-2 text-danger"></i>
                    Film Silme Onayı
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Bu filmi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                </div>
                <input type="hidden" id="deleteMovieId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteMovie">
                    <i class="fas fa-trash-alt me-2"></i>Evet, Sil
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="/js/admin/admin-movies.js"></script>
@endsection