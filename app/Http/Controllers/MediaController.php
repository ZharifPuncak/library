<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Category;
use App\Models\MediaDetail;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin      = (bool) (auth()->user()?->isAdmin());
        $activeStatus = $isAdmin
            ? strtolower((string) $request->input('status', 'all'))
            : 'published';
        if (!in_array($activeStatus, ['all', 'draft', 'published', 'archived'], true)) {
            $activeStatus = 'all';
        }

        $query = Collection::with(['categories', 'tags']);

        if ($activeStatus !== 'all') {
            $query->where('status', $activeStatus);
        } elseif (!$isAdmin) {
            $query->where('status', 'published');
        }

        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%$search%"));
            });
        }

        if ($request->categories) {
            $query->whereHas('categories', fn($q) =>
                $q->whereIn('categories.id', $request->categories)
            );
        }

        $tagFilter = array_filter((array) $request->input('tags', $request->input('tag') ? [$request->input('tag')] : []));
        if (!empty($tagFilter)) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagFilter));
        }

        $statusCounts = null;
        if ($isAdmin) {
            $countBase = Collection::query();
            if ($search = $request->input('search')) {
                $countBase->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%")
                      ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%$search%"));
                });
            }
            if ($request->categories) {
                $countBase->whereHas('categories', fn($q) =>
                    $q->whereIn('categories.id', $request->categories)
                );
            }
            if (!empty($tagFilter)) {
                $countBase->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagFilter));
            }

            $statusCounts = ['all' => (clone $countBase)->count()];
            foreach (['draft', 'published', 'archived'] as $s) {
                $statusCounts[$s] = (clone $countBase)->where('status', $s)->count();
            }
        }

        $sortDir     = strtolower((string) $request->input('sort')) === 'oldest' ? 'asc' : 'desc';
        $collections = $query->orderBy('created_at', $sortDir)->paginate(12)->withQueryString();

        $collectionNames = $collections->getCollection()->pluck('name');
        $mediaCounts = MediaDetail::where('key', 'collection')
            ->whereIn('value', $collectionNames)
            ->groupBy('value')
            ->selectRaw('value, count(*) as total')
            ->pluck('total', 'value');

        $categories = Category::all();
        $tags       = Tag::all();

        $requestedView = strtolower((string) $request->input('view'));
        $cookieView    = strtolower((string) $request->cookie('media_view', ''));
        $view          = in_array($requestedView, ['grid', 'list'], true)
            ? $requestedView
            : (in_array($cookieView, ['grid', 'list'], true) ? $cookieView : 'grid');

        $response = response()->view('users.all',
            compact('collections', 'mediaCounts', 'categories', 'tags', 'view', 'activeStatus', 'statusCounts')
        );

        if ($view !== $cookieView) {
            $response->cookie('media_view', $view, 60 * 24 * 30);
        }

        return $response;
    }
}
