<?php
/**
 * Created by rzian/scaffold.
 * User: @{USER}
 * Date: @{DATETIME}
 */

namespace @{NAMESPACE};

use Illuminate\Database\Eloquent\Model;

class @{NAME} extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = '@{TABLE}';
	
     /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

     /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [@{FILLABLES}
    ];

    /**
     * The attributes that are mass validatable
     *
     * @var array
     */
    public static $rules = [@{RULES}
    ];
    
@{RELATIONS}
}
