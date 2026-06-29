<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = ['request_id', 'company_id', 'task_description', 'scheduled_date', 'work_status'];

    public function company() {
        return $table->belongsTo(Company::class);
    }
}
