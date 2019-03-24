<?php

namespace Hanoivip\Ddd2\Models;

use Illuminate\Database\Eloquent\Model;

class TabAccount extends Model
{
    protected $connection = 'ipd';
    
    protected $table = 'tab_account';
    
    public $timestamps = false;
}
