<?php

namespace App\Providers;

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Type::addType('double', 'Doctrine\DBAL\Types\FloatType');
        Type::addType('tinyinteger', 'Doctrine\DBAL\Types\IntegerType');
        Blade::directive('money', function ($amount) {
            return "<?php echo '$' . number_format($amount, 2, ',', '.'); ?>";
        });
    }
}
