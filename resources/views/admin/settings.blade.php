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
            <h2>Accueil — second bouton du bandeau principal</h2>
            <p class="panel-sub">Le premier bouton du bandeau utilise l’action principale configurée dans la section Header.</p>
            <div class="field">
                <label for="values[home_secondary_cta_label]">Libellé</label>
                <input type="text" id="values[home_secondary_cta_label]" name="values[home_secondary_cta_label]" value="{{ old('values.home_secondary_cta_label', \App\Models\Setting::get('home_secondary_cta_label', 'Découvrir nos actions')) }}">
            </div>
            <div class="field">
                <label for="values[home_secondary_cta_url]">Lien</label>
                <input type="text" id="values[home_secondary_cta_url]" name="values[home_secondary_cta_url]" value="{{ old('values.home_secondary_cta_url', \App\Models\Setting::get('home_secondary_cta_url', '/programmes')) }}">
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

        <div class="panel">
            <h2>Actualités sur l’accueil</h2>
            <p class="panel-sub">Titres et lien du bloc qui met en avant les articles publiés.</p>
            <div class="field">
                <label for="values[blog_kicker]">Sur-titre</label>
                <input type="text" id="values[blog_kicker]" name="values[blog_kicker]" value="{{ old('values.blog_kicker', \App\Models\Setting::get('blog_kicker', 'Actualités')) }}">
            </div>
            <div class="field">
                <label for="values[blog_home_title]">Titre du bloc</label>
                <input type="text" id="values[blog_home_title]" name="values[blog_home_title]" value="{{ old('values.blog_home_title', \App\Models\Setting::get('blog_home_title', 'Les nouvelles du terrain')) }}">
            </div>
            <div class="field">
                <label for="values[blog_home_intro]">Introduction</label>
                <textarea id="values[blog_home_intro]" name="values[blog_home_intro]" rows="3">{{ old('values.blog_home_intro', \App\Models\Setting::get('blog_home_intro', '')) }}</textarea>
            </div>
            <div class="field">
                <label for="values[blog_home_link]">Libellé du lien vers le blog</label>
                <input type="text" id="values[blog_home_link]" name="values[blog_home_link]" value="{{ old('values.blog_home_link', \App\Models\Setting::get('blog_home_link', 'Toutes les actualités')) }}">
            </div>
            <div class="field">
                <label for="values[blog_page_title]">Titre de page du blog (navigateur et partage)</label>
                <input type="text" id="values[blog_page_title]" name="values[blog_page_title]" value="{{ old('values.blog_page_title', \App\Models\Setting::get('blog_page_title', 'Blog - Actualités et articles | AFRIYOL')) }}">
            </div>
            @php $blogCover = \App\Models\Setting::get('blog_hero_image', ''); @endphp
            <div class="field">
                <label for="values[blog_hero_image]">Photo de couverture du blog <span class="hint">Elle apparaît sur la page d’actualités. En l’absence de photo, le site utilise la photo du dernier article publié.</span></label>
                <input type="text" id="values[blog_hero_image]" name="values[blog_hero_image]" value="{{ old('values.blog_hero_image', $blogCover) }}" placeholder="URL d’une photo ou chemin du média">
                <label for="blog_hero_image_file" style="margin-top:8px;">... ou téléverser une photo</label>
                <input type="file" id="blog_hero_image_file" name="blog_hero_image_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                @error('blog_hero_image_file') <span class="error-text">{{ $message }}</span> @enderror
                @if ($blogCover)
                    <div class="media-current">
                        <img src="{{ \App\Support\Media::url($blogCover) }}" alt="Couverture actuelle du blog">
                        <label style="font-weight:400;"><input type="checkbox" name="remove_blog_hero_image" value="1"> Supprimer la photo enregistrée</label>
                    </div>
                @endif
            </div>
            <div class="field">
                <label for="values[blog_page_kicker]">Fil d’Ariane</label>
                <input type="text" id="values[blog_page_kicker]" name="values[blog_page_kicker]" value="{{ old('values.blog_page_kicker', \App\Models\Setting::get('blog_page_kicker', 'Blog')) }}">
            </div>
            <div class="field">
                <label for="values[blog_page_heading]">Titre de la page</label>
                <input type="text" id="values[blog_page_heading]" name="values[blog_page_heading]" value="{{ old('values.blog_page_heading', \App\Models\Setting::get('blog_page_heading', "Le blog d'AFRIYOL")) }}">
            </div>
            <div class="field">
                <label for="values[blog_page_intro]">Introduction de la page</label>
                <textarea id="values[blog_page_intro]" name="values[blog_page_intro]" rows="3">{{ old('values.blog_page_intro', \App\Models\Setting::get('blog_page_intro', "Actualités, retours d'expérience et coulisses de nos actions sur le terrain.")) }}</textarea>
            </div>
            <div class="field">
                <label for="values[blog_filter_all]">Libellé du filtre « tous »</label>
                <input type="text" id="values[blog_filter_all]" name="values[blog_filter_all]" value="{{ old('values.blog_filter_all', \App\Models\Setting::get('blog_filter_all', 'Tous les articles')) }}">
            </div>
        </div>

        <div class="panel">
            <h2>Libellés des pages d’articles et de réalisations</h2>
            <p class="panel-sub">Personnalisez les boutons et intertitres qui accompagnent chaque contenu détaillé.</p>
            @foreach ([
                'blog_nav_label' => ['Libellé dans le menu', 'Blog'],
                'blog_gallery_heading' => ['Titre de la galerie d’articles', 'Galerie'],
                'blog_back_button' => ['Bouton de retour au blog', 'Retour au blog'],
                'blog_contact_button' => ['Bouton de contact sur un article', 'Nous contacter'],
                'realisation_detail_kicker' => ['Sur-titre de la réalisation', 'Action de terrain'],
                'realisation_detail_result_label' => ['Libellé du résultat', 'Résultat clé'],
                'realisation_gallery_heading' => ['Titre de la galerie des réalisations', 'Photos de l’action'],
                'realisation_back_button' => ['Bouton de retour aux réalisations', 'Toutes les réalisations'],
                'realisation_contact_button' => ['Bouton de contact sur une réalisation', 'Soutenir nos actions'],
            ] as $key => [$label, $default])
                <div class="field">
                    <label for="values[{{ $key }}]">{{ $label }}</label>
                    <input type="text" id="values[{{ $key }}]" name="values[{{ $key }}]" value="{{ old('values.'.$key, \App\Models\Setting::get($key, $default)) }}">
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-green">Enregistrer les paramètres</button>
    </form>

@endsection
