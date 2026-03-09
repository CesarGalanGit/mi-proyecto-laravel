<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Car extends Model
{
    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'brand',
        'model',
        'year',
        'price',
        'mileage',
        'fuel_type',
        'transmission',
        'color',
        'city',
        'featured',
        'status',
        'source_name',
        'source_url',
        'source_external_id',
        'outbound_clicks',
        'last_synced_at',
        'thumbnail_url',
        'gallery',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'gallery' => 'array',
            'featured' => 'boolean',
            'outbound_clicks' => 'integer',
            'last_synced_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function referralClicks(): HasMany
    {
        return $this->hasMany(CarReferralClick::class);
    }

    public function searchableAs(): string
    {
        return (string) config('scout.algolia.index', 'cars');
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'name' => trim($this->brand.' '.$this->model),
            'year' => $this->year,
            'city' => $this->city,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'status' => $this->status,
            'price' => $this->price,
            'description' => $this->description,
        ];
    }
}
