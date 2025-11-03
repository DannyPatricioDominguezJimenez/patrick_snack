<?php
// App/Models/ProductCategory.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'color_code']; 

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}