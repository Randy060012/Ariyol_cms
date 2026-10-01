<?php

namespace App\Providers;

use App\Http\View\Composers\SiteComposer;
use App\Support\Media;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', SiteComposer::class);

        // @mediaUrl('storage/sections/x.jpg') -> URL publique, quelle que
        // soit la forme de la valeur (URL absolue, chemin stocké, asset
        // fourni). Volontairement pas « @media » : ce nom entrerait en
        // collision avec les règles CSS @media des feuilles de style.
        Blade::directive('mediaUrl', fn ($expression) => "<?php echo e(App\\Support\\Media::url({$expression})); ?>");
    }
}
