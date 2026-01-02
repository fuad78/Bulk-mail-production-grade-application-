<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipient;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    public function trackOpen($id)
    {
        $recipient = Recipient::find($id);

        if ($recipient && is_null($recipient->opened_at)) {
            $recipient->update(['opened_at' => now()]);
        }

        // Return 1x1 transparent GIF
        $content = base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
        return response($content, 200)->header('Content-Type', 'image/gif');
    }

    public function trackClick(Request $request, $id)
    {
        $recipient = Recipient::find($id);
        $targetUrl = $request->query('target');

        if (!$targetUrl) {
            abort(404);
        }

        if ($recipient) {
            if (is_null($recipient->clicked_at)) {
                $recipient->update(['clicked_at' => now()]);
            }
            // Update clicked_at every time? Or just first? 
            // Usually we want to know unique clicks for rates, but total clicks is also good.
            // For now, let's just stick to first click for the "Rate" calculation simplicity.
            // But we can update 'last_clicked_at' if we had it.
        }

        // Decode URL if it was base64 encoded to avoid breaking plain text emails
        $decodedUrl = base64_decode($targetUrl, true);
        if ($decodedUrl === false) {
            // Not base64, assume plain text (fallback)
            $finalUrl = $targetUrl;
        } else {
            $finalUrl = $decodedUrl;
        }

        // Basic validation to prevent open redirect vulnerabilities to arbitrary domains?
        // In a bulk email system, users WANT to redirect anywhere. 
        // We will trust the link provided by the campaign creator.

        return redirect()->away($finalUrl);
    }
}
