@extends('admin.layouts.app')

@section('title', 'Paramètres du site')

@section('content')

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="panel">
            <h2>Identité</h2>
            <p class="panel-sub">Ces informations sont utilisées dans le header et le footer du site public.</p>

            <div class="field">
                <label for="values[brand_name]">Nom de la structure</label>
                <input type="text" id="values[brand_name]" name="values[brand_name]" value="{{ old('values.brand_name', \App\Models\Setting::get('brand_name', 'AFRIYOL')) }}">
            </div>
            <div class="field">
                <label for="values[brand_tagline]">Slogan</label>
                <input type="text" id="values[brand_tagline]" name="values[brand_tagline]" value="{{ old('values.brand_tagline', \App\Models\Setting::get('brand_tagline', 'African Young Leaders')) }}">
            </div>
        </div>

        <div class="panel">
            <h2>Header</h2>

            <div class="field">
                <label for="values[header_cta_label]">Libellé du bouton d'action</label>
                <input type="text" id="values[header_cta_label]" name="values[header_cta_label]" value="{{ old('values.header_cta_label', \App\Models\Setting::get('header_cta_label', 'Rejoignez-nous')) }}">
            </div>
            <div class="field">
                <label for="values[header_cta_url]">Lien du bouton d'action</label>
                <input type="text" id="values[header_cta_url]" name="values[header_cta_url]" value="{{ old('values.header_cta_url', \App\Models\Setting::get('header_cta_url', '/contact')) }}">
            </div>
            <div class="field">
                <label for="logo_files[header_logo]">Logo du header <span class="hint">SVG ou PNG, fond clair recommandé. Laisser vide pour conserver le logo par défaut.</span></label>
                <input type="file" id="logo_files[header_logo]" name="logo_files[header_logo]" accept="image/svg+xml,image/png,image/jpeg">
                @error('logo_files.header_logo') <span class="error-text">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="panel">
            <h2>Footer</h2>

            <div class="field">
                <label for="values[footer_description]">Description courte</label>
                <textarea id="values[footer_description]" name="values[footer_description]" rows="3">{{ old('values.footer_description', \App\Models\Setting::get('footer_description', 'Organisation non gouvernementale dédiée aux Droits Humains, à la Paix, à l\'Environnement et au Leadership des Jeunes.')) }}</textarea>
            </div>
            <div class="field">
                <label for="values[footer_copyright]">Mention de copyright</label>
                <input type="text" id="values[footer_copyright]" name="values[footer_copyright]" value="{{ old('values.footer_copyright', \App\Models\Setting::get('footer_copyright', 'Copyright © '.date('Y').' African Young Leaders (AFRIYOL). Tous droits réservés.')) }}">
            </div>
            <div class="field">
                <label for="values[footer_president]">Mention de présidence</label>
                <input type="text" id="values[footer_president]" name="values[footer_president]" value="{{ old('values.footer_president', \App\Models\Setting::get('footer_president', 'Président : M. SILIVI Koffi Victor')) }}">
            </div>
        </div>

        <div class="panel">
            <h2>Coordonnées et réseaux</h2>

            <div class="field">
                <label for="values[contact_address]">Adresse du siège</label>
                <input type="text" id="values[contact_address]" name="values[contact_address]" value="{{ old('values.contact_address', \App\Models\Setting::get('contact_address', 'Tsévié, Daviémodji (Togo)')) }}">
            </div>
            <div class="field">
                <label for="values[contact_email]">Email de contact</label>
                <input type="text" id="values[contact_email]" name="values[contact_email]" value="{{ old('values.contact_email', \App\Models\Setting::get('contact_email', 'contact@afriyol.org')) }}">
            </div>
            <div class="field">
                <label for="values[social_linkedin]">URL LinkedIn</label>
                <input type="text" id="values[social_linkedin]" name="values[social_linkedin]" value="{{ old('values.social_linkedin', \App\Models\Setting::get('social_linkedin', 'https://www.linkedin.com/company/afriyol/')) }}">
            </div>
            <div class="field">
                <label for="values[social_facebook]">URL Facebook</label>
                <input type="text" id="values[social_facebook]" name="values[social_facebook]" value="{{ old('values.social_facebook', \App\Models\Setting::get('social_facebook', 'https://www.facebook.com/AfricanYoungLeadersAfriyol')) }}">
            </div>
            <div class="field">
                <label for="values[social_twitter]">URL X (Twitter)</label>
                <input type="text" id="values[social_twitter]" name="values[social_twitter]" value="{{ old('values.social_twitter', \App\Models\Setting::get('social_twitter', 'https://x.com/afriyol82635')) }}">
            </div>
        </div>

        <button type="submit" class="btn btn-green">Enregistrer les paramètres</button>
    </form>

@endsection
