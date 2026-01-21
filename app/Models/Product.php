<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use App\Models\Category;
use App\Models\VariationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    // protected $casts = [
    //     'variations' => 'array'
    // ];

    public function registerAllMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(100);
        $this->addMediaConversion('small')->width(480);

        $this->addMediaConversion('large')->width(1200);
       // $this->addMediaConversion('smal')->width(480);

    }

    public function scopeForVendor(Builder $query){
        return $query->where('created_by', auth()->user()->id);
    }

    public function scopePublished(Builder $query){
        return $query->where('status', ProductStatusEnum::Published->value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function variationTypes()
    {
        return $this->hasMany(VariationType::class);
    }

    public function variations(){
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
