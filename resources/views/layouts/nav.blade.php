<header class="site-header">
    <div class="wrap">
        <div class="nav-bar">
            <a href="{{ route('home') }}" class="brand">
                <img src="{{ asset($siteHeader['logo']) }}" alt="Logo {{ $siteHeader['brand_name'] }}" width="48" height="48">
                <span class="brand-name">
                    <strong>{{ $siteHeader['brand_name'] }}</strong>
                    <span>{{ $siteHeader['brand_tagline'] }}</span>
                </span>
            </a>

            <ul class="nav-links" id="navLinks">
                @foreach ($navPages as $item)
                    <li>
                        <a href="{{ $item['url'] }}"
                           class="{{ (url()->current() === url($item['url'])) ? 'is-active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'is-active' : '' }}">Blog</a>
                </li>
            </ul>

            <a href="{{ $siteHeader['cta_url'] }}" class="btn btn-primary nav-cta">{{ $siteHeader['cta_label'] }}</a>

            <button type="button" class="nav-toggle" aria-label="Ouvrir le menu" aria-controls="navLinks" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>
