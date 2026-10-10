<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()->ordered()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create(): View
    {
        return view('admin.reviews.create', ['review' => new Review(['rating' => 5, 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Review::query()->create($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Review created.');
    }

    public function edit(Review $review): View
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $review->update($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'description' => ['required', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'rating' => (int) $data['rating'],
            'description' => $data['description'],
            'sort_order' => $data['sort_order'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
