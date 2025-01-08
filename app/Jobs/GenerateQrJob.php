<?php

namespace App\Jobs;

use App\Models\Menu;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LaravelQRCode\Exceptions\EmptyTextException;
use LaravelQRCode\Exceptions\MalformedUrlException;
use LaravelQRCode\Facades\QRCode;
use PharIo\Version\Exception;

class GenerateQrJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Menu $menu)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $url = config('app.frontendUrl') . '/' . $this->menu->id;
            $filename = uniqid($this->menu->id . '_') . '.svg';
            $path = Storage::disk("public-qr")->path($filename);
            QRCode::text($url)->setOutfile($path)->svg();

            // Save to database qr code
            $this->menu->update([
                'qr' => $filename
            ]);

        } catch (EmptyTextException $e) {
            Log::error(
                'Message: {message}  | File: {file} | Line: {line} | Database rollback transaction',
                [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }
    }
}
