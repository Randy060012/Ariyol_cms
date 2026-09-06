<footer class="site-footer">
    <div class="wrap">
        <div class="footer-grid">

            <div class="footer-brand">
                <img src="{{ asset($siteFooter['logo']) }}" alt="Logo {{ $siteFooter['brand_name'] }}" width="52" height="52">
                <strong>{{ $siteFooter['brand_name'] }}</strong>
                <p>{{ $siteFooter['description'] }}</p>
            </div>

            <div>
                <h4>Navigation</h4>
                <ul class="footer-links">
                    @foreach ($navPages as $item)
                        <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4>Réseaux officiels</h4>
                <ul class="footer-links">
                    @if ($siteFooter['linkedin'])
                        <li><a href="{{ $siteFooter['linkedin'] }}" target="_blank" rel="noopener">LinkedIn</a></li>
                    @endif
                    @if ($siteFooter['facebook'])
                        <li><a href="{{ $siteFooter['facebook'] }}" target="_blank" rel="noopener">Facebook</a></li>
                    @endif
                    @if ($siteFooter['twitter'])
                        <li><a href="{{ $siteFooter['twitter'] }}" target="_blank" rel="noopener">X (Twitter)</a></li>
                    @endif
                </ul>
            </div>

            <div>
                <h4>Contact</h4>
                <div class="footer-meta">
                    <span>Siège : {{ $siteFooter['address'] }}</span>
                    <span><a href="mailto:{{ $siteFooter['email'] }}">{{ $siteFooter['email'] }}</a></span>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <span>{{ $siteFooter['copyright'] }}</span>
            <span>{{ $siteFooter['president'] }}</span>
        </div>
    </div>
</footer>
