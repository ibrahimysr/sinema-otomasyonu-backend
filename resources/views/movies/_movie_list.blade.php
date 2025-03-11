@foreach($movies as $movie)
<div class="col-lg-3 col-md-4 col-sm-6 movie-item" data-genres="{{ $movie->genre }}" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
    <div class="card h-100 movie-card">
        <div class="position-relative">
            <img src="{{ $movie->poster_url }}" class="card-img-top" alt="{{ $movie->title }}" style="height: 400px; object-fit: cover;">
            <div class="movie-overlay">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-primary">
                        <i class="fas fa-ticket-alt me-2"></i> Bilet Al
                    </a>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#trailerModal{{ $movie->id }}">
                        <i class="fas fa-play me-2"></i> Fragman
                    </button>
                </div>
            </div>
            <div class="movie-badges">
                <span class="badge bg-primary">{{ \Carbon\Carbon::parse($movie->release_date)->year }}</span>
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-star me-1"></i> {{ $movie->imdb_rating }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title text-truncate mb-1">{{ $movie->title }}</h5>
            <p class="card-text small text-muted mb-0">{{ Str::limit($movie->description, 100) }}</p>
        </div>
        <div class="card-footer bg-white border-top-0 pt-0">
            <div class="d-flex flex-wrap gap-1">
                @foreach(explode(',', $movie->genre) as $genre)
                <span class="badge bg-light text-dark">{{ trim($genre) }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Fragman Modal -->
    <div class="modal fade" id="trailerModal{{ $movie->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">{{ $movie->title }} - Fragman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $movie->trailer_url }}" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@if($movies->isEmpty())
<div class="col-12">
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i> Aradığınız kriterlere uygun film bulunamadı.
    </div>
</div>
@endif 