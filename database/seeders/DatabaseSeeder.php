<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedAdmin();
        $this->seedSettings();
        $this->seedPages();
    }

    private function seedAdmin(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@afriyol.org'],
            [
                'name' => 'Administrateur AFRIYOL',
                'password' => 'Afriyol2026!',
            ]
        );
    }

    private function seedSettings(): void
    {
        $settings = [
            'brand_name' => ['AFRIYOL', 'text', 'general'],
            'brand_tagline' => ['African Young Leaders', 'text', 'general'],
            'header_cta_label' => ['Rejoignez-nous', 'text', 'header'],
            'header_cta_url' => ['/contact', 'text', 'header'],
            'footer_description' => ['Organisation non gouvernementale dédiée aux Droits Humains, à la Paix, à l\'Environnement et au Leadership des Jeunes.', 'textarea', 'footer'],
            'footer_copyright' => ['Copyright © '.date('Y').' African Young Leaders (AFRIYOL). Tous droits réservés.', 'text', 'footer'],
            'footer_president' => ['Président : M. SILIVI Koffi Victor', 'text', 'footer'],
            'contact_address' => ['Tsévié, Daviémodji (Togo)', 'text', 'contact'],
            'contact_email' => ['contact@afriyol.org', 'text', 'contact'],
            'social_linkedin' => ['https://www.linkedin.com/company/afriyol/', 'text', 'contact'],
            'social_facebook' => ['https://www.facebook.com/AfricanYoungLeadersAfriyol', 'text', 'contact'],
            'social_twitter' => ['https://x.com/afriyol82635', 'text', 'contact'],
        ];

        foreach ($settings as $key => [$value, $type, $group]) {
            Setting::set($key, $value, $type, $group);
        }
    }

    private function seedPages(): void
    {
        $pages = [
            [
                'page_key' => 'home',
                'slug' => '',
                'title' => 'Accueil',
                'hero_kicker' => 'Organisation non gouvernementale - Togo',
                'hero_title' => 'African Young Leaders',
                'hero_subtitle' => 'Paix et Droits Autrement ! Nous réduisons la vulnérabilité des communautés, favorisons l\'insertion socioprofessionnelle des jeunes et luttons contre le changement climatique.',
                'sort_order' => 1,
                'sections' => [
                    ['facts', [
                        'kicker' => 'Notre mission',
                        'title' => 'Promouvoir les Droits Humains, la Paix et le Bien-être',
                        'items' => [
                            ['title' => '500+', 'description' => 'Plants mis en terre à Zéglé-Sagonou contre le changement climatique'],
                            ['title' => '1 200+', 'description' => 'Enfants et jeunes sensibilisés lors de la Journée de l\'Enfant Africain'],
                            ['title' => 'Vice-présidence', 'description' => 'Membre exécutif de la Plateforme Nationale RSE-SC TOGO'],
                        ],
                    ]],
                    ['cards', [
                        'kicker' => 'Nos axes majeurs',
                        'title' => 'Comment nous agissons sur le terrain',
                        'columns' => '3',
                        'items' => [
                            ['title' => 'Éducation & Mentorat', 'description' => 'Orientation scolaire et professionnelle, accompagnement des jeunes talents et prévention du chômage dès l\'école.'],
                            ['title' => 'Environnement & Climat', 'description' => 'Actions de reboisement, entretien des espaces publics, opérations Togo Propre et sensibilisation citoyenne.'],
                            ['title' => 'Paix, Droits & Inclusion', 'description' => 'Prévention de l\'extrémisme violent, promotion de la cohésion sociale et de l\'inclusion du genre.'],
                        ],
                    ]],
                    ['cards', [
                        'kicker' => 'Partenariats',
                        'title' => 'Nos partenaires et collaborateurs',
                        'columns' => '3',
                        'items' => [
                            ['title' => 'Les UST', 'description' => 'Universités Sociales du Togo - soutien aux ateliers de leadership et d\'art oratoire.'],
                            ['title' => 'Mairie Zio 1', 'description' => 'Partenariat institutionnel pour les opérations citoyennes de salubrité publique.'],
                            ['title' => 'WANEP-TOGO', 'description' => 'Réseau ouest-africain pour l\'édification de la paix et la prévention des conflits.'],
                            ['title' => 'Plan International', 'description' => 'Collaboration sur les campagnes de sensibilisation et la protection de l\'enfance.'],
                            ['title' => 'Afrik\'handi', 'description' => 'Accord média pour la promotion de l\'inclusion des personnes handicapées.'],
                            ['title' => 'RSE-SC TOGO', 'description' => 'Plateforme nationale de responsabilité sociétale des entreprises et de la société civile.'],
                        ],
                    ]],
                    ['cards', [
                        'kicker' => 'Activités récentes',
                        'title' => 'Retours sur nos dernières actions',
                        'columns' => '3',
                        'items' => [
                            ['title' => 'Journée Portes Ouvertes AFRIYOL', 'description' => '11 avril 2026 - Tsévié. Mobilisation des jeunes pour l\'engagement communautaire au centre IYF.'],
                            ['title' => 'Opération Togo Propre', 'description' => '7 mars 2026 - Zio 1. Curage des caniveaux et sensibilisation à la salubrité publique.'],
                            ['title' => 'Atelier Leadership & Art Oratoire', 'description' => '23 août 2025 - JOKO-TOGO. Formation du staff et des enfants de l\'orphelinat avec les UST.'],
                        ],
                    ]],
                    ['cta', [
                        'kicker' => 'Engagez-vous',
                        'title' => 'Nous investissons beaucoup dans l\'humain',
                        'text' => 'Chaque action, aussi petite soit-elle, contribue à bâtir une communauté plus forte, pacifique et résiliente. Rejoignez l\'aventure AFRIYOL.',
                        'button_label' => 'Contacter l\'association',
                        'button_url' => '/contact',
                    ]],
                ],
            ],

            [
                'page_key' => 'about',
                'slug' => 'a-propos',
                'title' => 'À propos',
                'hero_kicker' => 'African Young Leaders',
                'hero_title' => 'À propos d\'AFRIYOL',
                'hero_subtitle' => 'Paix et Droits Autrement.',
                'sort_order' => 2,
                'sections' => [
                    ['facts', [
                        'kicker' => 'Notre identité institutionnelle',
                        'title' => 'Reconnaissance officielle',
                        'items' => [
                            ['title' => 'N° 0681/MATDDT-DAG-DOCA', 'description' => 'Reconnaissance délivrée le 25 septembre 2024 par le Ministère de l\'Administration Territoriale du Togo'],
                            ['title' => 'Tsévié, Daviémodji', 'description' => 'Siège dans la Région Maritime, au service des communautés du Zio'],
                            ['title' => 'Jeunesse engagée', 'description' => 'Un collectif de jeunes leaders pour la paix, le civisme et l\'environnement'],
                        ],
                    ]],
                    ['quote', [
                        'quote' => 'Nous investissons beaucoup dans l\'humain. Face aux défis sécuritaires, climatiques et socio-économiques, notre responsabilité est d\'armer la jeunesse togolaise et africaine avec des compétences concrètes, des valeurs de paix et une vision d\'avenir durable.',
                        'author' => 'M. SILIVI Koffi Victor',
                        'role' => 'Président d\'AFRIYOL, Juriste et Défenseur Certifié des Droits de l\'Homme',
                    ]],
                    ['cards', [
                        'kicker' => 'Ancrage et alliances stratégiques',
                        'title' => 'Distinctions & représentations',
                        'columns' => '3',
                        'items' => [
                            ['title' => 'Plateforme RSE-SC TOGO', 'description' => 'AFRIYOL assure la vice-présidence du Bureau National.'],
                            ['title' => 'WANEP-TOGO & Youth4Peace', 'description' => 'Membre actif du réseau pour l\'édification de la paix.'],
                            ['title' => 'Protection de l\'enfance', 'description' => 'Membre du Cadre de Concertation des Acteurs de Protection de l\'Enfant dans le Zio.'],
                            ['title' => 'Forum Mondial de l\'Alimentation', 'description' => 'Initiateur du Chapitre National WFF Togo.'],
                        ],
                    ]],
                    ['cta', [
                        'kicker' => 'Engagez-vous',
                        'title' => 'Construisez l\'avenir avec nous',
                        'text' => 'Rejoignez un réseau de jeunes leaders engagés pour la paix et le développement.',
                        'button_label' => 'Nous contacter',
                        'button_url' => '/contact',
                    ]],
                ],
            ],

            [
                'page_key' => 'programmes',
                'slug' => 'programmes',
                'title' => "Domaines d'action",
                'hero_kicker' => 'Nos interventions',
                'hero_title' => "Nos domaines d'intervention",
                'hero_subtitle' => 'Cinq axes stratégiques d\'impact pour un changement durable.',
                'sort_order' => 3,
                'sections' => [
                    ['checklist', [
                        'kicker' => 'Axe 01',
                        'title' => 'Éducation, Leadership & Mentorat',
                        'lead' => 'Nous formons la prochaine génération de leaders capables de relever les défis de leurs communautés.',
                        'items' => [
                            ['title' => 'Mentorat et orientation scolaire dès les classes secondaires'],
                            ['title' => 'Prévention du chômage par le renforcement des compétences pratiques'],
                            ['title' => 'Formations en art oratoire, leadership et gouvernance associative'],
                        ],
                    ]],
                    ['checklist', [
                        'kicker' => 'Axe 02',
                        'title' => 'Environnement, Climat & Salubrité',
                        'lead' => 'Réponse directe à la crise climatique et promotion d\'un cadre de vie sain.',
                        'items' => [
                            ['title' => 'Campagnes de reboisement : projet 500+ plants à Zéglé-Sagonou'],
                            ['title' => 'Opérations citoyennes de salubrité (« Togo Propre » avec la Mairie Zio 1)'],
                            ['title' => 'Sensibilisation à l\'éco-citoyenneté et gestion des pépinières'],
                        ],
                    ]],
                    ['checklist', [
                        'kicker' => 'Axe 03',
                        'title' => 'Paix, Droits Humains & Cohésion Sociale',
                        'lead' => 'Prévenir la violence et promouvoir un climat d\'entente nationale.',
                        'items' => [
                            ['title' => 'Sensibilisation sur la prévention de l\'extrémisme violent et du terrorisme'],
                            ['title' => 'Promotion du civisme et de la justice citoyenne dans les écoles et villages'],
                            ['title' => 'Participation active au réseau WANEP-TOGO et Youth4Peace'],
                        ],
                    ]],
                    ['checklist', [
                        'kicker' => 'Axe 04',
                        'title' => 'Entrepreneuriat & Incubation Agricole',
                        'lead' => 'Stimuler l\'autonomie économique des jeunes et des femmes.',
                        'items' => [
                            ['title' => 'Initiations à l\'agrobusiness et à l\'entrepreneuriat vert (WFF Togo)'],
                            ['title' => 'Inclusion économique des sans-abris et des personnes vulnérables'],
                            ['title' => 'Projets de création d\'incubateurs agricoles locaux (Horizons 2026)'],
                        ],
                    ]],
                    ['checklist', [
                        'kicker' => 'Axe 05',
                        'title' => 'Genre & Inclusion Sociale',
                        'lead' => 'Garantir l\'égalité des chances sans distinction.',
                        'items' => [
                            ['title' => 'Inclusion active des personnes vivant avec un handicap (Partenariat Afrik\'handi)'],
                            ['title' => 'Lutte contre les violences basées sur le genre et autonomisation des jeunes filles'],
                        ],
                    ]],
                    ['cta', [
                        'kicker' => 'Agir avec nous',
                        'title' => 'Un projet, un partenariat, une question',
                        'text' => 'Notre équipe vous répond sur tous les domaines d\'intervention présentés ci-dessus.',
                        'button_label' => 'Nous écrire',
                        'button_url' => '/contact',
                    ]],
                ],
            ],

            [
                'page_key' => 'realisations',
                'slug' => 'realisations',
                'title' => 'Réalisations',
                'hero_kicker' => 'Notre historique',
                'hero_title' => 'Nos réalisations sur le terrain',
                'hero_subtitle' => 'Chronologie de nos actions et rapports d\'impact.',
                'sort_order' => 4,
                'sections' => [
                    ['facts', [
                        'kicker' => 'Chiffres clés',
                        'title' => 'Notre impact en quelques nombres',
                        'items' => [
                            ['title' => '500+', 'description' => 'Plants d\'arbres mis en terre et suivis à Zéglé-Sagonou'],
                            ['title' => '1 200+', 'description' => 'Jeunes sensibilisés lors de la Journée de l\'Enfant Africain 2025'],
                            ['title' => '6', 'description' => 'Actions majeures menées entre 2025 et 2026'],
                        ],
                    ]],
                    ['cards', [
                        'kicker' => 'Historique',
                        'title' => 'Historique des actions (2025 - 2026)',
                        'columns' => '2',
                        'items' => [
                            ['title' => '11 avril 2026 - Journée Portes Ouvertes', 'description' => 'Session de mobilisation de la jeunesse au Centre IYF de Tsévié pour promouvoir l\'engagement citoyen.'],
                            ['title' => '7 mars 2026 - Opération « Togo Propre »', 'description' => 'Curage des caniveaux à Tsévié et accord média avec Afrik\'handi pour l\'inclusion des personnes handicapées.'],
                            ['title' => '31 janvier 2026 - Suivi écologique', 'description' => 'Inspection des 500+ plants de Zéglé-Sagonou et salubrité publique à Attikoumé avec Le Potentiel.'],
                            ['title' => '23 août 2025 - Leadership & art oratoire', 'description' => 'Atelier pour les membres et les enfants de l\'orphelinat JOKO-TOGO, avec le soutien des UST.'],
                            ['title' => '16 juin 2025 - Journée de l\'Enfant Africain', 'description' => 'Sensibilisation sur l\'éducation et la protection de l\'enfance auprès de plus de 1 200 jeunes.'],
                        ],
                    ]],
                    ['cta', [
                        'kicker' => 'Prochaine étape',
                        'title' => 'Participez à la prochaine action',
                        'text' => 'Bénévoles, partenaires et porteurs de projets sont les bienvenus.',
                        'button_label' => 'Devenir bénévole',
                        'button_url' => '/contact',
                    ]],
                ],
            ],

            [
                'page_key' => 'contact',
                'slug' => 'contact',
                'title' => 'Contact',
                'hero_kicker' => 'Nous rejoindre',
                'hero_title' => 'Contactez-nous',
                'hero_subtitle' => 'Construisons ensemble un avenir empreint de paix et d\'opportunités.',
                'sort_order' => 5,
                'sections' => [
                    ['cta', [
                        'kicker' => 'Coordonnées officielles',
                        'title' => 'Une question, une candidature, un partenariat',
                        'text' => 'Écrivez-nous via le formulaire ci-dessous ou contactez-nous directement : contact@afriyol.org - Siège : Tsévié, Daviémodji (Togo).',
                    ]],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            $sections = $pageData['sections'];
            unset($pageData['sections']);

            $page = Page::updateOrCreate(
                ['page_key' => $pageData['page_key']],
                $pageData + ['is_published' => true]
            );

            if ($page->sections()->exists()) {
                continue; // Ne pas écraser les modifications faites via l'admin
            }

            foreach ($sections as $index => [$type, $data]) {
                $page->sections()->create([
                    'type' => $type,
                    'data' => $data,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
