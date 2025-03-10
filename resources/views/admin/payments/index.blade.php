@extends('admin.layouts.app')

@section('title', 'Ödemeler')

@section('actions')
<button type="button" class="btn btn-primary animate__animated animate__fadeInRight" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
    <i class="fas fa-plus"></i> Yeni Ödeme Ekle
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
                            <h6 class="card-subtitle mb-2 text-muted">Toplam Ödemeler</h6>
                            <h2 class="card-title mb-0" id="totalPayments">0</h2>
                        </div>
                        <div class="stats-icon bg-primary">
                            <i class="fas fa-money-bill-wave"></i>
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
                            <h6 class="card-subtitle mb-2 text-muted">Başarılı Ödemeler</h6>
                            <h2 class="card-title mb-0" id="successfulPayments">0</h2>
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
                            <h6 class="card-subtitle mb-2 text-muted">Bekleyen Ödemeler</h6>
                            <h2 class="card-title mb-0" id="pendingPayments">0</h2>
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
                            <h6 class="card-subtitle mb-2 text-muted">İptal Edilen Ödemeler</h6>
                            <h2 class="card-title mb-0" id="cancelledPayments">0</h2>
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
                    <label for="searchUser" class="form-label">Müşteri</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <select class="form-select" id="searchUser">
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
                <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                Ödeme Listesi
            </h5>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-secondary" id="refreshPayments">
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
                            <th><i class="fas fa-user me-2"></i>Müşteri</th>
                            <th><i class="fas fa-ticket-alt me-2"></i>Bilet</th>
                            <th><i class="fas fa-money-bill me-2"></i>Tutar</th>
                            <th><i class="fas fa-credit-card me-2"></i>Ödeme Yöntemi</th>
                            <th><i class="fas fa-calendar me-2"></i>Tarih</th>
                            <th><i class="fas fa-check-circle me-2"></i>Durum</th>
                            <th><i class="fas fa-cog me-2"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody">
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

<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Yeni Ödeme Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="addPaymentForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">Müşteri</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Müşteri Seçin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="ticket_id" class="form-label">Bilet</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                <select class="form-select" id="ticket_id" name="ticket_id" required>
                                    <option value="">Bilet Seçin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Tutar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                                <span class="input-group-text">₺</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Ödeme Yöntemi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Ödeme Yöntemi Seçin</option>
                                    <option value="credit_card">Kredi Kartı</option>
                                    <option value="debit_card">Banka Kartı</option>
                                    <option value="cash">Nakit</option>
                                    <option value="transfer">Havale/EFT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Durum</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending">Bekliyor</option>
                                    <option value="completed">Tamamlandı</option>
                                    <option value="cancelled">İptal Edildi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notlar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="savePaymentBtn">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel">
                    <i class="fas fa-edit me-2 text-info"></i>
                    Ödeme Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form id="editPaymentForm">
                    <input type="hidden" id="edit_payment_id" name="payment_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_payment_code" class="form-label">Ödeme Kodu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                <input type="text" class="form-control" id="edit_payment_code" name="payment_code" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_user_id" class="form-label">Müşteri</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <select class="form-select" id="edit_user_id" name="user_id" required>
                                    <option value="">Müşteri Seçin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_ticket_id" class="form-label">Bilet</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                <select class="form-select" id="edit_ticket_id" name="ticket_id" required>
                                    <option value="">Bilet Seçin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_amount" class="form-label">Tutar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" required>
                                <span class="input-group-text">₺</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_payment_method" class="form-label">Ödeme Yöntemi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                <select class="form-select" id="edit_payment_method" name="payment_method" required>
                                    <option value="">Ödeme Yöntemi Seçin</option>
                                    <option value="credit_card">Kredi Kartı</option>
                                    <option value="debit_card">Banka Kartı</option>
                                    <option value="cash">Nakit</option>
                                    <option value="transfer">Havale/EFT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">Durum</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="pending">Bekliyor</option>
                                    <option value="completed">Tamamlandı</option>
                                    <option value="cancelled">İptal Edildi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notlar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="updatePaymentBtn">
                    <i class="fas fa-save me-2"></i>Güncelle
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePaymentModal" tabindex="-1" aria-labelledby="deletePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePaymentModalLabel">
                    <i class="fas fa-trash me-2 text-danger"></i>
                    Ödeme Sil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <p>Bu ödemeyi silmek istediğinizden emin misiniz?</p>
                <input type="hidden" id="delete_payment_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeletePaymentBtn">
                    <i class="fas fa-trash me-2"></i>Sil
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-labelledby="viewPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPaymentModalLabel">
                    <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                    Ödeme Detayları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="payment-details">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-hashtag me-2"></i>Ödeme Kodu:</strong> <span id="paymentCode"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-check-circle me-2"></i>Durum:</strong> <span id="paymentStatus"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-user me-2"></i>Müşteri:</strong> <span id="paymentCustomer"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-ticket-alt me-2"></i>Bilet:</strong> <span id="paymentTicket"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-money-bill me-2"></i>Tutar:</strong> <span id="paymentAmount"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-credit-card me-2"></i>Ödeme Yöntemi:</strong> <span id="paymentMethod"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-calendar me-2"></i>Tarih:</strong> <span id="paymentDate"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <p><strong><i class="fas fa-sticky-note me-2"></i>Notlar:</strong></p>
                                    <div id="paymentNotes" class="p-3 bg-light rounded"></div>
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
                <button type="button" class="btn btn-primary" id="printPaymentBtn">
                    <i class="fas fa-print me-2"></i>Yazdır
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="/js/admin/admin-payments.js"></script>
@endsection 