<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslations;

class Posts extends Model
{
    use HasFactory,HasTranslations;
    public $translatable = ['title','description','link_name','subtitle'];
    protected $guarded = [];
    protected $table = 'posts';

    public function getImageLinkAttribute(){
        if(str_contains($this->image, "http")){
            return $this->image;
        }else {
            if(substr( $this->image, 0, 8) === "/images/"){
                return $this->image;
            }else{
                return config('app.company_images').$this->image."_large.jpg";
            }
            
        }
        
    }
}
