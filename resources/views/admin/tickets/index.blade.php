@extends('admin.layouts.app')

@section('title', 'Biletler')

@section('actions')
<button type="button" class="btn btn-primary animate__animated animate__fadeInRight" data-bs-toggle="modal" data-bs-target="#addTicketModal">
    <i class="fas fa-plus"></i> Yeni Bilet Ekle
</button>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card dashboard-card stats-card animate__animated animate__fadeInUp">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Toplam Biletler</h6>
                            <h2 class="card-title mb-0" id="totalTickets">0</h2>
                        </div>
                        <div class="stats-icon bg-primary">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card stats-card animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Onaylanan Biletler</h6>
                            <h2 class="card-title mb-0" id="completedTickets">0</h2>
                        </div>
                        <div class="stats-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card stats-card animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Rezerve Edilen Biletler</h6>
                            <h2 class="card-title mb-0" id="pendingTickets">0</h2>
                        </div>
                        <div class="stats-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card stats-card animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">İptal Edilen Biletler</h6>
                            <h2 class="card-title mb-0" id="cancelledTickets">0</h2>
                        </div>
                        <div class="stats-icon bg-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-card mb-4 animate__animated animate__fadeInDown">
        <div class="card-body">
            <form id="searchForm" class="row g-3">
                <div class="col-md-3">
                    <label for="searchTicketCode" class="form-label">Bilet Kodu</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                        <input type="text" class="form-control" id="searchTicketCode" placeholder="Bilet kodu ara...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="searchMovie" class="form-label">Film</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-film"></i></span>
                        <select class="form-select" id="searchMovie">
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
                <div class="col-md-2">
                    <label for="searchStatus" class="form-label">Durum</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                        <select class="form-select" id="searchStatus">
                            <option value="">Tümü</option>
                            <option value="completed">Tamamlandı</option>
                            <option value="pending">Bekliyor</option>
                            <option value="cancelled">İptal Edildi</option>
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
                <i class="fas fa-ticket-alt me-2 text-primary"></i>
                Bilet Listesi
            </h5>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-secondary" id="refreshTickets">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="ticketsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>ID</th>
                            <th><i class="fas fa-barcode me-2"></i>Kod</th>
                            <th><i class="fas fa-film me-2"></i>Film</th>
                            <th><i class="fas fa-building me-2"></i>Sinema</th>
                            <th><i class="fas fa-door-open me-2"></i>Salon</th>
                            <th><i class="fas fa-clock me-2"></i>Seans</th>
                            <th><i class="fas fa-chair me-2"></i>Koltuk</th>
                            <th><i class="fas fa-user me-2"></i>Müşteri</th>
                            <th><i class="fas fa-money-bill me-2"></i>Fiyat</th>
                            <th><i class="fas fa-check-circle me-2"></i>Durum</th>
                            <th><i class="fas fa-cog me-2"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody">
                        <tr>
                            <td colspan="11" class="text-center py-4">
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

<div class="modal fade" id="addTicketModal" tabindex="-1" aria-labelledby="addTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTicketModalLabel">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Yeni Bilet Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="addTicketForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="showtime_id" class="form-label">Seans</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                <select class="form-select" id="showtime_id" name="showtime_id" required>
                                    <option value="">Seans Seçin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">Müşteri</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Müşteri Seçin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="seat_number" class="form-label">Koltuk Numarası</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-chair"></i></span>
                                <input type="text" class="form-control" id="seat_number" name="seat_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Fiyat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                <span class="input-group-text">₺</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Durum</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="reserved">Rezerve Edildi</option>
                                    <option value="confirmed">Onaylandı</option>
                                    <option value="cancelled">İptal Edildi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveTicketBtn">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editTicketModal" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTicketModalLabel">
                    <i class="fas fa-edit me-2 text-info"></i>
                    Bilet Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="editTicketForm">
                    <input type="hidden" id="edit_ticket_id" name="ticket_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_showtime_id" class="form-label">Seans</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                <select class="form-select" id="edit_showtime_id" name="showtime_id" required>
                                    <option value="">Seans Seçin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_user_id" class="form-label">Müşteri</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <select class="form-select" id="edit_user_id" name="user_id" required>
                                    <option value="">Müşteri Seçin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_seat_number" class="form-label">Koltuk Numarası</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-chair"></i></span>
                                <input type="text" class="form-control" id="edit_seat_number" name="seat_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_price" class="form-label">Fiyat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                                <span class="input-group-text">₺</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">Durum</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="reserved">Rezerve Edildi</option>
                                    <option value="confirmed">Onaylandı</option>
                                    <option value="cancelled">İptal Edildi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="updateTicketBtn">
                    <i class="fas fa-save me-2"></i>Güncelle
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTicketModal" tabindex="-1" aria-labelledby="deleteTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTicketModalLabel">
                    <i class="fas fa-trash me-2 text-danger"></i>
                    Bilet Sil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <p>Bu bileti silmek istediğinizden emin misiniz?</p>
                <input type="hidden" id="delete_ticket_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteTicketBtn">
                    <i class="fas fa-trash me-2"></i>Sil
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewTicketModal" tabindex="-1" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTicketModalLabel">
                    <i class="fas fa-ticket-alt me-2 text-primary"></i>
                    Bilet Detayları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div id="ticketMoviePoster" class="mb-3">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="ticket-details">
                            <h4 id="ticketMovieTitle" class="mb-3">Film Adı</h4>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-barcode me-2"></i>Bilet Kodu:</strong> <span id="ticketCode"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-check-circle me-2"></i>Durum:</strong> <span id="ticketStatus"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-building me-2"></i>Sinema:</strong> <span id="ticketCinema"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-door-open me-2"></i>Salon:</strong> <span id="ticketHall"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-calendar me-2"></i>Tarih:</strong> <span id="ticketDate"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-clock me-2"></i>Saat:</strong> <span id="ticketTime"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-chair me-2"></i>Koltuk:</strong> <span id="ticketSeat"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-money-bill me-2"></i>Fiyat:</strong> <span id="ticketPrice"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <p><strong><i class="fas fa-user me-2"></i>Müşteri:</strong> <span id="ticketCustomer"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Kapat
                </button>
                <button type="button" class="btn btn-primary" id="printTicketBtn">
                    <i class="fas fa-print me-2"></i>Yazdır
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="/js/admin/admin-tickets.js"></script>
@endsection 