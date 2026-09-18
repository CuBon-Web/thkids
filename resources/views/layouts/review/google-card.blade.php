@php
    $reviewName = trim(strip_tags(languageName($review->name ?? '')));
    $reviewPosition = trim(strip_tags(languageName($review->position ?? '')));
    $reviewContent = trim(strip_tags(languageName($review->content ?? '')));
    $avatarUrl = $review->avatar ?? '';
    if ($avatarUrl && !preg_match('#^https?://#i', $avatarUrl)) {
        $avatarUrl = url(ltrim($avatarUrl, '/'));
    }
    $initials = '';
    if ($reviewName) {
        foreach (preg_split('/\s+/u', $reviewName, 3, PREG_SPLIT_NO_EMPTY) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }
    }
@endphp
<article class="google-review-card">
    <header class="google-review-card__head">
        <div class="google-review-card__author">
            <div class="google-review-card__avatar" aria-hidden="true">
                @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="" loading="lazy" decoding="async">
                @else
                <span>{{ $initials ?: 'K' }}</span>
                @endif
            </div>
            <div class="google-review-card__identity">
                <h4 class="google-review-card__name">{{ $reviewName ?: 'Khách hàng' }}</h4>
                <div class="google-review-card__stars" aria-label="5 sao">
                    @for ($s = 0; $s < 5; $s++)
                    <i class="bi bi-star-fill"></i>
                    @endfor
                </div>
            </div>
        </div>
        <span class="google-review-card__badge" title="Đánh giá trên Google">
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
        </span>
    </header>

    @if($reviewContent)
    <p class="google-review-card__text">{{ $reviewContent }}</p>
    @endif

    @if($reviewPosition)
    <footer class="google-review-card__meta">
        <i class="bi bi-geo-alt"></i> {{ $reviewPosition }}
    </footer>
    @endif
</article>
