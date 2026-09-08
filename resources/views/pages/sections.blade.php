@php
    $d = $section->data ?? [];
    $kicker = $d['kicker'] ?? null;
    $title = $d['title'] ?? null;
    $items = $d['items'] ?? [];
    $image = !empty($d['image']) ? $d['image'] : null;
    // Galerie multi-images de la section (gérée depuis l'administration).
    $images = !empty($d['images']) && is_array($d['images']) ? $d['images'] : [];
    // Logos des partenaires (section « partners » uniquement).
    $logos = !empty($d['logos']) && is_array($d['logos']) ? $d['logos'] : [];
@endphp

@php
    // Alternate paper background for rhythm; CTA uses navy.
    $isCta = $section->type === 'cta';
    $paper = !$isCta && ($loop->index ?? 0) % 2 === 1;
@endphp

@php
    // Image banner shared by facts / cards / checklist / cta.
    $imageBanner = fn (?string $src) => $src
        ? '<img src="'.e(asset($src)).'" alt="" loading="lazy" style="width: 100%; aspect-ratio: 21/9; object-fit: cover; border: 1px solid var(--border);">'
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

        @if ($section->type === 'cards')
            @if ($kicker || $title)
                <div class="section-head {{ ($d['columns'] ?? 3) == 2 ? '' : 'center' }}">
                    @if ($kicker)<span class="kicker">{{ $kicker }}</span>@endif
                    @if ($title)<h2 class="h-section">{{ $title }}</h2>@endif
                </div>
            @endif
            {!! $imageBanner($image) !!}
            @if ($image)<div style="height: 28px;"></div>@endif
            <div class="grid-3">
                @foreach ($items as $item)
                    <div class="rule-card {{ $loop->odd ? '' : 'green-top' }}">
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($section->type === 'checklist')
            <div class="grid-2">
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
            @if ($image)
                <div style="margin-top: 32px;">{!! $imageBanner($image) !!}</div>
            @endif
        @endif

        @if ($section->type === 'quote')
            <div class="grid-2" style="align-items: center;">
                @if ($image)
                    <img src="{{ asset($image) }}" alt="" loading="lazy" style="width: 100%; aspect-ratio: 4/3; object-fit: cover; border: 1px solid var(--border);">
                @endif
                <blockquote style="border-left: 3px solid var(--green); padding-left: 24px;">
                    <p style="font-size: 17px; line-height: 1.7;">{{ $d['quote'] }}</p>
                    <footer class="muted small" style="margin-top: 14px;">
                        {{ $d['author'] }}@if (!empty($d['role'])) &mdash; {{ $d['role'] }}@endif
                    </footer>
                </blockquote>
            </div>
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
            @if (!empty($logos))
                <div class="logo-marquee" aria-label="Logos de nos partenaires">
                    <div class="logo-track">
                        {{-- Le contenu est dupliqué en deux groupes identiques : le
                             déplacement de -50% correspond alors exactement à un
                             groupe, pour un défilement parfaitement continu. --}}
                        @for ($copy = 0; $copy < 2; $copy++)
                            <div class="logo-group" @if ($copy === 1) aria-hidden="true" @endif>
                                @foreach ($logos as $logo)
                                    <span class="logo-item"><img src="{{ asset($logo) }}" alt="" loading="lazy"></span>
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
                        <img src="{{ asset($img) }}" alt="" loading="lazy" data-lightbox>
                    </figure>
                @endforeach
            </div>
        @endif

    </div>
</section>
