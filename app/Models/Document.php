<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';
    protected $primaryKey = 'doc_id';

    protected $fillable = [
        'report_id',
        'original_name',
        'file_path',
        'upload_by'
    ];

    /**
     * Upload file dan simpan path serta nama asli ke DB.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function uploadDokumen(UploadedFile $file, $reportId, $uploadBy)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('documents', $filename, 'public');

        $this->original_name = $file->getClientOriginalName();
        $this->file_path = 'documents/' . $filename;
        $this->report_id = $reportId;
        $this->upload_by = $uploadBy;
        $this->save();
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id', 'report_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upload_by', 'id');
    }
}
