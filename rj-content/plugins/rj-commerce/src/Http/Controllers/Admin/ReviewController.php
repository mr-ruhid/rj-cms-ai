<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['product.translations', 'user'])->latest()->paginate(20);
        return view('plugin-rj-commerce::admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', __('Review approved successfully.'));
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', __('Review rejected successfully.'));
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', __('Review deleted successfully.'));
    }
}
