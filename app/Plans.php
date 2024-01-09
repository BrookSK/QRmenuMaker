<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasConfig;

class Plans extends Model
{
    use SoftDeletes;
    use HasConfig;

    protected $modelName="App\Plans";
    protected $table = 'plan';
}
