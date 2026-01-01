<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasImages;
use App\Traits\HasSlug;
class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, HasImages, HasSlug;
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'status',
        'is_home',
        'is_menu',
        'is_footer',
        'position',
        'meta_description',
        'meta_keywords',
        'meta_image'
    ];
    protected $casts = [
        'status'     => 'boolean',
        'is_home'    => 'boolean',
        'is_menu'    => 'boolean',
        'is_footer'  => 'boolean',
        'parent_id'  => 'integer',
        'position'   => 'integer',
    ];
    const TYPE_PHYSICS        = 'physics';
    const TYPE_SERVICE        = 'services';

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->position) || $model->position === 0) {
                $model->position = static::max('position') + 1;
            }
        });
    }
    
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id')->whereNotNull('parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }

    /**
     * Lấy tất cả ID của các danh mục con (cháu, chắt...) một cách đệ quy.
     */
    public function getAllDescendantIds()
    {
        $descendantIds = [];
        foreach ($this->children as $child) {
            $descendantIds[] = $child->id;
            // Kêu gọi đệ quy để lấy ID của các cấp sâu hơn
            $descendantIds = array_merge($descendantIds, $child->getAllDescendantIds());
        }
        return $descendantIds;
    }

    public function latestProducts(){
        return $this->hasMany(Product::class)->where('status',1)->latest()->limit(10);
    }
}