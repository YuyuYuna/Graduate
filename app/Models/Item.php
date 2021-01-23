<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    const BACHELOR = '學士';
    const MASTER = '碩士';
    const DOCTOR = '博士';

    const COLOR_ITEMS = ['領巾', '披肩、帽穗'];
    const SIZE_ITEMS = ['學士服', '碩士服', '博士服'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'pivot',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'remain_quantity',
    ];

    public function getRemainQuantityAttribute()
    {
        $color_out = Set::all()
            ->where('color_item', $this->id)
            ->where('returned', false)
            ->count();

        $size_out = Set::all()
            ->where('size_item', $this->id)
            ->where('returned', false)
            ->count();

        return $this->quantity - $color_out - $size_out;
    }

    public function Orders()
    {
        return $this->belongsToMany('App\Models\Order', 'order_items')
            ->as('orders');
    }
}
