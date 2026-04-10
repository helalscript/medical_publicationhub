<?php

namespace App\Services\API\V1\User\Faqs;

use App\Models\Category;
use App\Models\Faq;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserFaqService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Fetch all resources.
     *
     * @return mixed
     */
    public function index($request)
    {
        try {
            $per_page = $request->per_page ?? 25;

            // Backward compatibility: if client passes `type`, return faqs for that type.
            $type = $request->type ?? null;
            if ($type) {
                $category = $this->getCategoryByType($type);
                if (! $category) {
                    return [];
                }

                return Faq::select('id', 'question', 'answer')
                    ->where('category_id', $category->id)
                    ->where('status', 'active')
                    ->paginate($per_page);
            }

            return Faq::select('id', 'question', 'answer', 'category_id')
                ->where('status', 'active')
                ->paginate($per_page);
        } catch (Exception $e) {
            Log::error('UserFaqService::index'.$e->getMessage());
            throw $e;
        }
    }

    public function getCategoryByType(string $type): ?Category
    {
        return Category::where('type', $type)
            ->where('status', 'active')
            ->first();
    }

    public function getFaqsByCategoryType(string $type, int $per_page = 25)
    {
        $category = $this->getCategoryByType($type);
        if (! $category) {
            return [];
        }

        return Faq::select('id', 'question', 'answer')
            ->where('category_id', $category->id)
            ->where('status', 'active')
            ->paginate($per_page);
    }

    public function getAllCategories()
    {
        return Category::where('status', 'active')
            ->where('type', 'faq')
            ->orderBy('id')
            ->select('id', 'name')
            ->get();
    }

    public function getCategoryById(string $id)
    {
        $category = Category::where('status', 'active')
            ->where('type', 'faq')
            ->where('status', 'active')
            ->where('type', 'faq')
            ->with('faqs:id,question,answer,category_id')
            ->find($id);
        if (! $category) {
            throw new Exception('Category not found');
        }

        return $category;
    }
}
