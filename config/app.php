<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\Filament\AppPanelProvider::class,
        App\Providers\RouteServiceProvider::class,

        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        // App\Providers\Filament\AppPanelProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,
        App\Providers\SocialstreamServiceProvider::class,
        \SocialiteProviders\Manager\ServiceProvider::class,
        Jackiedo\Cart\CartServiceProvider::class,

    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        'Cart' => Jackiedo\Cart\Facades\Cart::class,
    ])->toArray(),


    'communities' => json_decode(env('COMMUNITIES')),
    'object_types' => json_decode(env('OBJECT_TYPES')),
    'how_did_u_learn' => json_decode(env('HOW_DID_U_LEARN')),
    'vocabulary_groups' => [
        'readiness_level' => 'g5',
        'support_type' => 'g6',
        'e-rihs_platform' => 'g7',
        'offered_to' => 'g8',
        'person_role' => 'g9',
        'research_disciplines' => 'g10',
        'link_type' => 'g11',
        'period_unit' => 'g12',
        'operating_language' => 'g13',
        'period_class' => 'g14',
        'provider_role' => 'g15',
        'e-rihs_national_nodes' => 'g16',
        'working_distance' => 'g17',
        'object_impact' => 'g18',
        'reference_role' => 'g19',
        'personal_title' => 'g20',
        'persistent_identifier' => 'g21',
        'technique' => 'g22',
        'material' => 'g35',
        'measurable_property' => 'g40',
        'tool_role' => 'g41',
        'country' => 'g42',
        'institution_country' => 'g42',
        'licence_type' => 'g44',
    ],

    'archlab_document_types' => json_decode(env('ARCHLAB_DOCUMENT_TYPES', '{}')),

    'elastic_index' => env('ELASTIC_INDEX', 'erihs_services'),

    'min_reviewers' => env('MIN_REVIEWERS', 3),
    'number_of_calls_in_chart_widget' => env('NUMBER_OF_CALLS_IN_CHART_WIDGET', 5),


    'proposal_evaluation_weight' => [
        'excellence_relevance' => env('PROPOSAL_EVALUATION_WEIGHT_EXCELLENCE_RELEVANCE', 0.13393),
        'excellence_methodology' => env('PROPOSAL_EVALUATION_WEIGHT_EXCELLENCE_METHODOLOGY', 0.13393),
        'excellence_originality' => env('PROPOSAL_EVALUATION_WEIGHT_EXCELLENCE_ORIGINALITY', 0.13393),
        'excellence_expertise' => env('PROPOSAL_EVALUATION_WEIGHT_EXCELLENCE_EXPERTISE', 0.13393),
        'excellence_timeliness' => env('PROPOSAL_EVALUATION_WEIGHT_EXCELLENCE_TIMELINESS', 0.10714),
        'excellence_state_of_the_art' => env('PROPOSAL_EVALUATION_WEIGHT_EXCELLENCE_STATE_OF_THE_ART', 0.10714),

        'impact_research' => env('PROPOSAL_EVALUATION_WEIGHT_IMPACT_RESEARCH', 0.10416),
        'impact_knowledge_sharing' => env('PROPOSAL_EVALUATION_WEIGHT_IMPACT_KNOWLEDGE_SHARING', 0.04167),
        'impact_innovation_potential' => env('PROPOSAL_EVALUATION_WEIGHT_IMPACT_INNOVATION_POTENTIAL', 0.04167),
        'impact_open_access' => env('PROPOSAL_EVALUATION_WEIGHT_IMPACT_OPEN_ACCESS', 0.04167),
        'impact_expected_impacts' => env('PROPOSAL_EVALUATION_WEIGHT_IMPACT_EXPECTED_IMPACTS', 0.02083),

    ],
];
