<?php

namespace Loaf\Settings;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Loaf\Base\Contracts\Settings\SettingsManager;

class Setting extends Model
{
    /**
     * @var array the value casts to an array
     */
    protected $casts = [
        'value' => 'array',
    ];

    protected $fillable = [
        'path', 'scope', 'type', 'value',
    ];

    /**
     * Boot up the Setting model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('scope', function (Builder $builder) {
            $scopes = self::getSettingsScopes()->filter();
            $builder->whereIn('scope', $scopes);
            $builder->orderBy('scope', 'desc');
        });
    }

    /**
     * Returns an ordered collection of setting scopes.
     *
     * @return Collection
     */
    protected static function getSettingsScopes() : Collection
    {
        return collect(app(SettingsManager::class)::getDefaultScope());
    }
}
