<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A "share my pet's medical report" request captured via the WhatsApp chatbot.
 */
class MedicalReportRequest extends Model
{
    protected $table = 'medical_report_requests';

    protected $fillable = [
        'wa_id', 'client_name', 'patient_name', 'patient_uid', 'mobile', 'report_name', 'extra', 'status',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    /** Public-facing reference, e.g. #MR-0042. */
    public function reference(): string
    {
        return 'MR-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
