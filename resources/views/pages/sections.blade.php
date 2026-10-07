@php
    $d = $section->data ?? [];
    $kicker = $d['kicker'] ?? null;
    $title = $d['title'] ?? null;
    $items = $d['items'] ?? [];
    $image = !empty($d['image']) && !\App\Support\Media::isBrowserCapture($d['image']) ? \App\Support\Media::url($d['image']) : null;
    $authorImage = !empty($d['author_image']) && !\App\Support\Media::isBrowserCapture($d['author_image']) ? \App\Support\Media::url($d['author_image']) : null;
    // Galerie multi-images de la section (gérée depuis l'administration).
    $images = !empty($d['images']) && is_array($d['images'])
        ? array_values(array_filter($d['images'], fn ($path) => !\App\Support\Media::isBrowserCapture($path)))
        : [];
    // Logos des partenaires (section « partners » uniquement).
    $logos = !empty($d['logos']) && is_array($d['logos'])
        ? array_values(array_filter($d['logos'], fn ($path) => !\App\Support\Media::isBrowserCapture($path)))
        : [];
    // Grille de cartes pilotée par le réglage « colonnes » du CMS.
    $cardsColumns = (int) ($d['columns'] ?? 3);
@endphp

@php
    // Alternate paper background for rhythm; CTA uses navy.
    $isCta = $section->type === 'cta';
    $paper = !$isCta && ($loop->index ?? 0) % 2 === 1;
@endphp

@php
    // Image banner shared by facts / cards / checklist / cta / partners.
    $imageBanner = fn (?string $src) => $src
        ? '<img src="'.e($src).'" alt="" loading="lazy" style="width: 100%; aspect-ratio: 21/9; object-fit: cover; border: 1px solid var(--border);">'
        : '';
@endphp

<section class="pad-section {{ $paper ? 'paper' : '' }} {{ $isCta ? 'on-dark' : '' }}">
    <div class="wrap">

        @if ($section->type === 'facts')
            <div class="section-head">
                @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
            </div>
            {!! $imageBanner($image) !!}
            @if ($image)<div style="height: 28px;"></div>@endif
            <div class="grid-3">
                @foreach ($items as $item)
                    <div class="fact">
                        <strong>{{ $item['title'] }}</strong>
                        <span>{{ $item['description'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($section->type === 'timeline')
            <div class="section-head">
                @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                @if (!empty($d['lead']))<p>{{ $d['lead'] }}</p>@endif
            </div>
            @if ($image)<div class="timeline-banner">{!! $imageBanner($image) !!}</div>@endif
            <ol class="impact-timeline">
                @foreach ($items as $item)
                    <li><span class="timeline-date">{{ $item['date'] ?? '' }}</span><div><h3>{{ $item['title'] ?? '' }}</h3><p>{{ $item['description'] ?? '' }}</p></div></li>
                @endforeach
            </ol>
        @endif

        @if (in_array($section->type, ['cards', 'engagement'], true))
            @if ($kicker || $title)
                <div class="section-head {{ $cardsColumns === 2 ? '' : 'center' }}">
                    @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                    @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                </div>
            @endif
            {!! $imageBanner($image) !!}
            @if ($image)<div style="height: 28px;"></div>@endif
            <div class="{{ $cardsColumns === 2 ? 'grid-2' : 'grid-3' }}">
                @foreach ($items as $item)
                    <article class="rule-card {{ $loop->odd ? '' : 'green-top' }} {{ $section->type === 'engagement' ? 'engagement-card' : '' }}">
                        @if (!empty($item['image']) && !\App\Support\Media::isBrowserCapture($item['image']))
                            <div class="card-media"><img src="{{ \App\Support\Media::url($item['image']) }}" alt="" loading="lazy" data-lightbox></div>
                        @endif
                        <h3>@if(!empty($item['url']))<a href="{{ $item['url'] }}">{{ $item['title'] }}</a>@else{{ $item['title'] }}@endif</h3>
                        <p>{{ $item['description'] }}</p>
                        @if (!empty($item['url']))<a class="text-link" href="{{ $item['url'] }}">{{ $item['button_label'] ?? $section->field('button_label', $section->type === 'engagement' ? 'Choisir cette option' : 'En savoir plus') }} <span aria-hidden="true">→</span></a>@endif
                    </article>
                @endforeach
            </div>
        @endif

        @if ($section->type === 'checklist')
            @if ($image)
                <div class="ngo-axis-layout {{ (($loop->index ?? 0) % 2) ? 'is-reversed' : '' }}">
                    <figure class="ngo-axis-photo"><img src="{{ $image }}" alt="{{ $title ?: 'Illustration de nos actions' }}" loading="lazy" data-lightbox></figure>
                    <div class="ngo-axis-copy">
                        @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                        @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                        @if (!empty($d['lead']))<p class="lead">{{ $d['lead'] }}</p>@endif
                        <ul class="check-list">
                            @foreach ($items as $item)
                                <li>{{ $item['title'] }}@if (!empty($item['description'])) &mdash; {{ $item['description'] }}@endif</li>
                            @endforeach
                        </ul>
                        @if (!empty($d['button_label']))
                            <p class="ngo-axis-action"><a href="{{ $d['button_url'] ?? route('about') }}" class="btn btn-primary">{{ $d['button_label'] }} <span aria-hidden="true">→</span></a></p>
                        @endif
                    </div>
                </div>
            @else
                <div class="grid-2 ngo-axis-text-only">
                    <div>
                        @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                        @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                        @if (!empty($d['lead']))<p class="lead" style="margin-top: 14px;">{{ $d['lead'] }}</p>@endif
                    </div>
                    <ul class="check-list">
                        @foreach ($items as $item)
                            <li>{{ $item['title'] }}@if (!empty($item['description'])) &mdash; {{ $item['description'] }}@endif</li>
                        @endforeach
                    </ul>
                </div>
                @if (!empty($d['button_label']))
                    <p class="ngo-axis-action"><a href="{{ $d['button_url'] ?? route('about') }}" class="btn btn-primary">{{ $d['button_label'] }} <span aria-hidden="true">→</span></a></p>
                @endif
            @endif
        @endif

        @if ($section->type === 'quote')
            <div class="president-message {{ $image ? 'has-illustration' : ($authorImage ? 'portrait-only' : 'no-visual') }}">
                <div class="president-message-visual">
                    @if ($image)<img class="president-message-photo" src="{{ $image }}" alt="Photo illustrant le message de {{ $d['author'] ?? 'l’association' }}" loading="lazy" data-lightbox>@endif
                    @if ($authorImage)
                        <figure class="president-portrait">
                            <img src="{{ $authorImage }}" alt="Portrait de {{ $d['author'] ?? 'l’auteur du message' }}" loading="lazy" data-lightbox>
                        </figure>
                    @endif
                </div>
                <blockquote class="president-message-copy">
                    <span class="kicker">Message de la présidence</span>
                    <p>{{ $d['quote'] ?? '' }}</p>
                    <footer>
                        <strong>{{ $d['author'] ?? '' }}</strong>
                        @if (!empty($d['role']))<span>{{ $d['role'] }}</span>@endif
                    </footer>
                </blockquote>
            </div>
        @endif

        @if ($section->type === 'realisations')
            {{--
                Réalisations : chaque action est affichée en ligne avec la
                photo à gauche et le texte à droite. Le contenu vient des
                « Réalisations » gérées depuis l'administration ; la section
                contrôle le titre, le nombre affiché et un bouton facultatif.
            --}}
            @php
                $realisations = $realisationResults ?? \App\Models\Realisation::forDisplay((int) ($d['limit'] ?? 0));
            @endphp

            @if ($kicker || $title)
                <div class="section-head">
                    @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                    @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                </div>
            @endif

            @if ($realisationResults ?? false)
                <form method="GET" action="{{ route('realisations') }}" class="realisation-filters" aria-label="Filtrer les réalisations">
                    <label><span>{{ $d['filter_theme_label'] ?? 'Domaine' }}</span>
                        <select name="theme">
                            <option value="">{{ $d['filter_all_themes_label'] ?? 'Tous les domaines' }}</option>
                            @foreach ($realisationThemes as $theme)<option value="{{ $theme }}" @selected($activeTheme === $theme)>{{ $theme }}</option>@endforeach
                        </select>
                    </label>
                    <label><span>{{ $d['filter_location_label'] ?? 'Lieu' }}</span>
                        <select name="lieu">
                            <option value="">{{ $d['filter_all_locations_label'] ?? 'Tous les lieux' }}</option>
                            @foreach ($realisationLocations as $location)<option value="{{ $location }}" @selected($activeLocation === $location)>{{ $location }}</option>@endforeach
                        </select>
                    </label>
                    <button type="submit" class="btn btn-primary">{{ $d['filter_submit_label'] ?? 'Filtrer' }}</button>
                    @if ($activeTheme || $activeLocation)<a href="{{ route('realisations') }}" class="text-link">{{ $d['filter_clear_label'] ?? 'Effacer les filtres' }}</a>@endif
                </form>
            @endif

            @if ($realisations->isEmpty())
                <p class="muted small">{{ $d['empty_label'] ?? 'Les réalisations seront affichées ici dès leur ajout depuis l’administration.' }}</p>
            @else
                <div class="realisation-cards">
                    @foreach ($realisations as $realisation)
                        @php $hasRealisationPhoto = $realisation->image && !\App\Support\Media::isBrowserCapture($realisation->image); @endphp
                        <article class="realisation-card">
                            @if ($hasRealisationPhoto)
                                <a class="realisation-card-image" href="{{ route('realisations.show', $realisation->slug) }}">
                                    <img src="{{ \App\Support\Media::url($realisation->image) }}" alt="{{ $realisation->title }}" loading="lazy">
                                    @if ($realisation->category)<span class="realisation-tag">{{ $realisation->category }}</span>@endif
                                </a>
                            @endif
                            <div class="realisation-card-body">
                                <p class="realisation-meta">{{ $realisation->category }}@if ($realisation->category && ($realisation->formattedDate() || $realisation->location)) · @endif{{ $realisation->formattedDate() }}@if ($realisation->formattedDate() && $realisation->location) · @endif{{ $realisation->location }}</p>
                                <h3><a href="{{ route('realisations.show', $realisation->slug) }}">{{ $realisation->title }}</a></h3>
                                @if ($realisation->impact)<strong class="realisation-impact">{{ $realisation->impact }}</strong>@endif
                                @if ($realisation->description)<p>{{ \Illuminate\Support\Str::limit($realisation->description, 150) }}</p>@endif
                                <a class="text-link" href="{{ route('realisations.show', $realisation->slug) }}">{{ $d['item_link_label'] ?? 'Découvrir cette action' }} <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
                @if ($realisations instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                    <div class="blog-pagination">{{ $realisations->links() }}</div>
                @endif
            @endif

            @if (!empty($d['button_label']))
                <p style="text-align: center; margin-top: 36px;">
                    <a href="{{ $d['button_url'] ?? '/realisations' }}" class="btn btn-ghost">{{ $d['button_label'] }}</a>
                </p>
            @endif
        @endif

        @if ($section->type === 'cta')
            <div class="section-head center">
                @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                @if (!empty($d['text']))<p>{{ $d['text'] }}</p>@endif
            </div>
            @if ($image)
                <div style="max-width: 880px; margin: 0 auto 32px;">{!! $imageBanner($image) !!}</div>
            @endif
            @if (!empty($d['button_label']))
                <p style="text-align: center;">
                    <a href="{{ $d['button_url'] ?? route('contact') }}" class="btn btn-light">{{ $d['button_label'] }}</a>
                </p>
            @endif
        @endif

        @if ($section->type === 'news')
            @php
                $latestPosts = \App\Models\Post::tableExists()
                    ? \App\Models\Post::published()->ordered()->limit((int) ($d['limit'] ?? 3))->get()
                    : collect();
            @endphp
            <div class="section-head {{ $cardsColumns === 2 ? '' : 'center' }}">
                @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                @if (!empty($d['lead']))<p>{{ $d['lead'] }}</p>@endif
            </div>
            @if ($latestPosts->isEmpty())
                <p class="muted small">Les actualités publiées apparaîtront ici.</p>
            @else
                <div class="{{ $cardsColumns === 2 ? 'grid-2 news-grid' : 'news-grid' }}">
                    @foreach ($latestPosts as $post)
                        @php $hasPostPhoto = $post->main_image && !\App\Support\Media::isBrowserCapture($post->main_image); @endphp
                        <article class="news-story-card">
                            @if ($hasPostPhoto)<a class="news-story-image" href="{{ route('blog.show', ['post' => $post->slug]) }}"><img src="{{ \App\Support\Media::url($post->main_image) }}" alt="{{ $post->title }}" loading="lazy"></a>@endif
                            <div class="news-story-body">
                                <p class="news-story-meta">{{ $post->category ?: $post->formattedDate() }}@if($post->category && $post->formattedDate()) · {{ $post->formattedDate() }}@endif</p>
                                <h3><a href="{{ route('blog.show', ['post' => $post->slug]) }}">{{ $post->title }}</a></h3>
                                @if($post->excerpt)<p>{{ \Illuminate\Support\Str::limit($post->excerpt, 145) }}</p>@endif
                                <a class="text-link" href="{{ route('blog.show', ['post' => $post->slug]) }}">{{ $d['item_link_label'] ?? 'Lire l’article' }} <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
            @if (!empty($d['button_label']))
                <div class="section-action">
                    <a href="{{ $d['button_url'] ?? route('blog.index') }}" class="btn btn-ghost">{{ $d['button_label'] }} <span aria-hidden="true">→</span></a>
                </div>
            @endif
        @endif

        @if ($section->type === 'partners')
            {{--
                Partenariats : bandeau de logos des partenaires en défilement
                horizontal continu. Remplace l'ancienne section « Cartes de
                contenu - Partenariats » (conservée en commentaire dans le
                DatabaseSeeder). Logos gérés depuis l'administration,
                au niveau de la page d'accueil.
            --}}
            <div class="section-head center">
                @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
            </div>
            @if ($image)
                <div style="max-width: 880px; margin: 0 auto 32px;">{!! $imageBanner($image) !!}</div>
            @endif
            @if (!empty($logos))
                <div class="logo-marquee" aria-label="Logos de nos partenaires">
                    <div class="logo-track">
                        {{-- Le contenu est dupliqué en deux groupes identiques : le
                             déplacement de -50% correspond alors exactement à un
                             groupe, pour un défilement parfaitement continu. --}}
                        @for ($copy = 0; $copy < 2; $copy++)
                            <div class="logo-group" @if ($copy === 1) aria-hidden="true" @endif>
                                @foreach ($logos as $logo)
                                    <span class="logo-item"><img src="{{ \App\Support\Media::url($logo) }}" alt="" loading="lazy"></span>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            @else
                <p class="muted small" style="text-align: center;">Les logos de nos partenaires seront affichés ici dès leur ajout depuis l'administration.</p>
            @endif
        @endif

        {{-- Galerie d'images de la section (endroits réservés pour l'ajout d'images). --}}
        @if (!empty($images))
            <div class="gallery-grid">
                @foreach ($images as $img)
                    <figure class="gallery-item">
                        <img src="{{ \App\Support\Media::url($img) }}" alt="" loading="lazy" data-lightbox>
                    </figure>
                @endforeach
            </div>
        @endif

    </div>
</section>
