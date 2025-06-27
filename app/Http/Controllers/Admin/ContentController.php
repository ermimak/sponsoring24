<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\News;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function index()
    {
        $featuredProjects = Project::where('is_featured', true)
            ->orderBy('featured_order')
            ->get();
            
        $availableProjects = Project::where('is_featured', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('Admin/Content/Index', [
            'featuredProjects' => $featuredProjects,
            'availableProjects' => $availableProjects
        ]);
    }
    
    public function showNews($id)
    {
        $news = News::findOrFail($id);
        return Inertia::render('NewsShow', [
            'news' => $news
        ]);
    }

    public function featuredProjects()
    {
        $featuredProjects = Project::where('is_featured', true)
            ->orderBy('featured_order')
            ->get();
            
        $availableProjects = Project::where('is_featured', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('Admin/Content/FeaturedProjects', [
            'featuredProjects' => $featuredProjects,
            'availableProjects' => $availableProjects
        ]);
    }
    
    public function updateFeatured(Request $request)
    {
        $validated = $request->validate([
            'featured_project_ids' => 'required|array',
            'featured_project_ids.*' => 'required|exists:projects,id',
        ]);
        
        // First, reset all projects to not featured
        Project::query()->update(['is_featured' => false, 'featured_order' => null]);
        
        // Then update the selected projects as featured with their order
        foreach ($validated['featured_project_ids'] as $index => $projectId) {
            Project::where('id', $projectId)->update([
                'is_featured' => true,
                'featured_order' => $index
            ]);
        }
        
        // Log the activity
        UserActivityService::logAdmin('featured_projects_updated', [
            'updated_by' => Auth::id(),
            'featured_project_ids' => collect($validated['featured_project_ids'])->pluck('id')->toArray(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ], Auth::id());
        
        return redirect()->back()->with('success', 'Featured projects updated successfully');
    }

    public function news()
    {
        $news = News::all();
        return Inertia::render('Admin/Content/News', [
            'news' => $news
        ]);
    }

    public function updateNews(Request $request)
    {
        $validated = $request->validate([
            'news' => 'required|array',
            'news.*.id' => 'required|exists:news,id',
            'news.*.title' => 'required|string',
            'news.*.content' => 'required|string',
            'news.*.image_url' => 'nullable|file|image|max:2048',
            'news.*.is_published' => 'required|boolean',
        ]);

        foreach ($validated['news'] as $news) {
            if ($news['image_url']) {
                if ($news['image_url']) {
                    Storage::disk('public')->delete($news['image_url']);
                }
                $path = $request->file('image_url')->store('news', 'public');
                $news['image_url'] = $path;
            }
            News::where('id', $news['id'])->update([
                'title' => $news['title'],
                'excerpt' => $news['excerpt'],
                'content' => $news['content'],
                'image_url' => $news['image_url'],
                'is_published' => $news['is_published']
            ]);
        }

        // Log the activity
        UserActivityService::logAdmin('news_updated', [
            'updated_by' => Auth::id(),
            'news' => $validated['news'],
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ], Auth::id());

        return redirect()->back()->with('success', 'News updated successfully');
    }

    public function createNews(Request $request)
    {
        // validate request
        $validated = $request->validate([
            'title' => 'required|string',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'nullable|file|image|max:2048',
            'is_published' => 'required|boolean',
        ]);

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('news', 'public');
            $validated['image_url'] = $path;
        }

        $news = News::create([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'image_url' => $validated['image_url'],
            'is_published' => $validated['is_published']
        ]);

        

        // Log the activity
        UserActivityService::logAdmin('news_created', [
            'created_by' => Auth::id(),
            'news' => $news,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ], Auth::id());

        return redirect()->back()->with('success', 'News created successfully');
    }

    public function destroyNews($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        // Log the activity
        UserActivityService::logAdmin('news_deleted', [
            'deleted_by' => Auth::id(),
            'news' => $news,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ], Auth::id());

        return redirect()->back()->with('success', 'News deleted successfully');
    }


}
