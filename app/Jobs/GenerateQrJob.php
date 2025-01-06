<?php

namespace App\Jobs;

use App\Models\Menu;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $url = route('public.menus.show', $this->menu);
        $qr = QrCode::format('png')->size(500)->maring(10)->generate($url);
        $name = Storage::disk('public')->put('qr', $qr);

        $this->menu->update([
           'qr' => $name
        ]);
    }
}
