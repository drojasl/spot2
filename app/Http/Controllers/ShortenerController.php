<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\ShortenedUrl;
use App\Repositories\ShortenedUrlRepositoryInterface;

class ShortenerController extends Controller
{
    public function __construct(protected ShortenedUrlRepositoryInterface $shortenedUrlRepository)
    {

    }

    /**
     * Display a listing of shortened URLs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $urls = $this->shortenedUrlRepository->findByUserId(auth()->id(), 5, $currentPage);
        return Inertia::render('Dashboard', [
            'urls' => $urls,
        ]);
    }

    /**
     * Store a newly created shortened URL in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $this->shortenedUrlRepository->create([
                'url' => $request->input('url'),
                'shortened' => Str::random(8),
                'user_id' => Auth::id(),
            ]);
    
            return redirect()->route('dashboard')->with('success', 'URL shortened successfully!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to shorten URL. Please try again.');
        }
    }

    public function destroy($id)
    {
        if ($this->shortenedUrlRepository->delete($id, Auth::id())) {
            return response()->json(['message' => 'URL deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'URL not found or you do not have permission to delete this URL.'], 404);
        }
    }

    public function go($short)
    {
        $shortenedUrl = $this->shortenedUrlRepository->findByShortened($short);

        if ($shortenedUrl) {
            return redirect()->away($shortenedUrl->url);
        }

        return redirect()->route('dashboard')->with('error', 'URL not found.');
    }
}
