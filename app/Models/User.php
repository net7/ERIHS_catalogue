<?php

namespace App\Models;


use App\Services\UserService;
use App\Traits\CordraInterface;
use App\Traits\HasCordra;
use App\Traits\HasRolesTrait;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JoelButcher\Socialstream\HasConnectedAccounts;
use JoelButcher\Socialstream\SetsProfilePhotoFromUrl;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Tags\HasTags;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasName, CordraInterface

{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto {
        profilePhotoUrl as getPhotoUrl;
    }
    use HasConnectedAccounts;
    use Notifiable;
    use SetsProfilePhotoFromUrl;
    use TwoFactorAuthenticatable;
    use HasRolesTrait;
    use Impersonate;
    use HasTags;
    use HasCordra;


    public const USER_ROLE = 'user';
    public const ADMIN_ROLE = 'admin';
    public const REVIEWER_ROLE = 'reviewer';
    public const SERVICE_MANAGER = 'service_manager';
    public const HELP_DESK_ROLE = 'help desk';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'nationality',
        'birth_year',
        'gender',
        'home_institution',
        'home_institution_id',
        'institution_address',
        'institution_city',
        'institution_status_code',
        'institution_country',
        'job',
        'academic_background',
        'position',
        'office_phone',
        'mobile_phone',
        'email',
        'city',
        'country',
        'mailing_address',
        'first_login',
        'short_cv',
        'disciplines',
        'terms_of_service',
        'confidentiality',
        'number_of_reviews',
        'api_token',
        'object_types',
        'password'
    ];


    public static $mandatoryFields = [
        'name',
        'surname',
        'birth_year',
        'gender',
        'home_institution',
        'institution_address',
        'institution_city',
        'institution_status_code',
        'job',
        'academic_background',
        'position',
        'email',
        'city',
        'country',
        'short_cv',
    ];

    public static $additionalMandatoryFieldsByRole = [
        self::USER_ROLE => [],
        self::REVIEWER_ROLE => [],
        self::ADMIN_ROLE => [],
        self::SERVICE_MANAGER => [],
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'disciplines' => 'array',
        'object_types' => 'array'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getFullNameEmailAttribute(): string
    {
        return "{$this->name} {$this->surname} ({$this->email})";
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function profilePhotoUrl(): Attribute
    {
        return filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)
            ? Attribute::get(fn() => $this->profile_photo_path)
            : $this->getPhotoUrl();
    }

    // it means it can access the admin backend
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        if ($this->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE])) {
            return true;
        }

        return false;
    }
    public function canBeImpersonated()
    {
        if ($this->hasRole(User::ADMIN_ROLE)) {
            return false;
        }
        return true;
    }

    public function proposals()
    {
        return $this->belongsToMany(Proposal::class, 'applicant_proposal', 'applicant_id', 'proposal_id')->withPivot('leader', 'alias');
    }

    public function proposalReviewer()
    {
        return $this->belongsToMany(Proposal::class, 'proposal_reviewer', 'reviewer_id', 'proposal_id');
    }

    public function organizationUsers(): HasMany
    {
        return $this->hasMany(OrganizationUser::class);
    }


    public function organizations()
    {
        return $this->belongsToMany(
            related: Organization::class,
            table: 'organization_user',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'organization_id',
        );
    }


    public function getFilamentName(): string
    {
        return "{$this->name} {$this->surname}";
    }

    public function toCordraJson()
    {
        return UserService::createJsonToSend($this);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_manager_service');
    }
}
