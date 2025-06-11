<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\News;
use App\Models\Project;
use App\Models\Content;
use App\Notifications\ContentActionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function dashboard(Request $request)
    {
        $pendingUsers = User::where('approval_status', 'pending')->count();
        $totalUsers = User::count();
        $contentItems = News::count();
        
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get latest 10 notifications for the admin
        $user = $request->user();
        $notifications = $user->notifications()->latest()->limit(10)->get();
        
        // Count unread notifications
        $unreadCount = $user->unreadNotifications()->count();
            
        return Inertia::render('Admin/Dashboard', [
            'pendingUsers' => $pendingUsers,
            'totalUsers' => $totalUsers,
            'contentItems' => $contentItems,
            'recentUsers' => $recentUsers,
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadCount,
        ]);
    }

    /**
     * Display the content management page.
     */
    public function contentIndex()
    {
        return Inertia::render('Admin/Content/Index');
    }

    /**
     * Display the news management page.
     */
    public function newsIndex()
    {
        $news = News::orderBy('published_at', 'desc')->get();
        
        return Inertia::render('Admin/Content/News', [
            'news' => $news,
        ]);
    }

    /**
     * Store a new news item.
     */
    public function storeNews(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image_url' => 'nullable|url|max:2000',
            'is_published' => 'boolean',
        ]);
        
        $news = new News();
        $news->title = $validated['title'];
        $news->excerpt = $validated['excerpt'];
        $news->content = $validated['content'];
        $news->image_url = $validated['image_url'];
        $news->is_published = $validated['is_published'];
        $news->published_at = $validated['is_published'] ? now() : null;
        $news->save();
        
        // Send notification to admin users about new news item
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        foreach ($admins as $admin) {
            if ($admin->id !== Auth::id()) { // Don't notify the admin who created the news
                $admin->notify(new ContentActionNotification(
                    $news,
                    'news',
                    'created',
                    [
                        'updated_by' => Auth::user()->name,
                        'is_published' => $validated['is_published']
                    ]
                ));
            }
        }
        
        return redirect()->route('admin.content.news')
            ->with('success', 'News item created successfully.');
    }

    /**
     * Update an existing news item.
     */
    public function updateNews(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image_url' => 'nullable|url|max:2000',
            'is_published' => 'boolean',
        ]);
        
        // Track what fields were updated
        $updatedFields = [];
        if ($news->title != $validated['title']) $updatedFields[] = 'title';
        if ($news->excerpt != $validated['excerpt']) $updatedFields[] = 'excerpt';
        if ($news->content != $validated['content']) $updatedFields[] = 'content';
        if ($news->image_url != $validated['image_url']) $updatedFields[] = 'image';
        
        // Check if publication status changed
        $statusChanged = $news->is_published != $validated['is_published'];
        $action = 'updated';
        if ($statusChanged) {
            $action = $validated['is_published'] ? 'published' : 'unpublished';
        }
        
        // If publishing for the first time, set published_at
        if ($validated['is_published'] && !$news->is_published) {
            $news->published_at = now();
        }
        
        $news->title = $validated['title'];
        $news->excerpt = $validated['excerpt'];
        $news->content = $validated['content'];
        $news->image_url = $validated['image_url'];
        $news->is_published = $validated['is_published'];
        $news->save();
        
        // Send notification to admin users about updated news item
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        foreach ($admins as $admin) {
            if ($admin->id !== Auth::id()) { // Don't notify the admin who updated the news
                $admin->notify(new ContentActionNotification(
                    $news,
                    'news',
                    $action,
                    [
                        'updated_by' => Auth::user()->name,
                        'updated_fields' => $updatedFields,
                        'is_published' => $validated['is_published']
                    ]
                ));
            }
        }
        
        return redirect()->route('admin.content.news')
            ->with('success', 'News item updated successfully.');
    }

    /**
     * Delete a news item.
     */
    public function destroyNews(News $news)
    {
        // Store news data before deletion for notification
        $newsData = [
            'id' => $news->id,
            'title' => $news->title,
            'excerpt' => $news->excerpt
        ];
        
        // Create a copy of the news for notification
        $newsCopy = clone $news;
        
        $news->delete();
        
        // Send notification to admin users about deleted news item
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        foreach ($admins as $admin) {
            if ($admin->id !== Auth::id()) { // Don't notify the admin who deleted the news
                $admin->notify(new ContentActionNotification(
                    $newsCopy,
                    'news',
                    'deleted',
                    [
                        'updated_by' => Auth::user()->name,
                        'deleted_at' => now()->toDateTimeString()
                    ]
                ));
            }
        }
        
        return redirect()->route('admin.content.news')
            ->with('success', 'News item deleted successfully.');
    }

    /**
     * Display the featured projects management page.
     */
    public function featuredProjectsIndex()
    {
        try {
            // Check if the 'featured' column exists
            $featuredProjects = Project::where('featured', true)->get();
            $availableProjects = Project::where('featured', false)
                ->where('public_donation_enabled', true)
                ->get();
        } catch (\Exception $e) {
            // If the column doesn't exist, return empty collections
            $featuredProjects = collect([]);
            $availableProjects = Project::where('public_donation_enabled', true)->get();
        }
        
        // Format project data to ensure proper display
        $featuredProjects = $featuredProjects->map(function($project) {
            return $this->formatProjectData($project);
        });
        
        $availableProjects = $availableProjects->map(function($project) {
            return $this->formatProjectData($project);
        });
        
        return Inertia::render('Admin/Content/FeaturedProjects', [
            'featuredProjects' => $featuredProjects,
            'availableProjects' => $availableProjects,
        ]);
    }

    /**
     * Update featured projects.
     */
    /**
     * Format project data for display in the frontend
     */
    private function formatProjectData($project)
    {
        return [
            'id' => $project->id,
            'title' => $project->name ?? $project->title ?? 'Unnamed Project',
            'description' => $project->short_description ?? $project->description ?? 'No description available',
            'image_square' => $project->image_square ?? $project->image ?? null,
            'featured' => $project->featured ?? false
        ];
    }

    public function updateFeaturedProjects(Request $request)
    {
        $validated = $request->validate([
            'featured_project_ids' => 'required|array',
            'featured_project_ids.*' => 'exists:projects,id',
        ]);
        
        // Update or create the featured projects content entry
        Content::updateOrCreate(
            ['section' => 'featured_projects'],
            [
                'title' => 'Featured Projects',
                'subtitle' => 'Projects highlighted on the homepage',
                'content' => json_encode([
                    'project_ids' => $validated['featured_project_ids'],
                ]),
                'is_active' => true,
                'order' => 1,
            ]
        );
        
        DB::beginTransaction();
        
        try {
            try {
                // Reset all projects to not featured
                Project::where('featured', true)->update(['featured' => false]);
                
                // Set selected projects as featured
                Project::whereIn('id', $validated['featured_project_ids'])
                    ->update(['featured' => true]);
            } catch (\Exception $e) {
                // If the column doesn't exist, just store the featured project IDs in the Content table
                // This will still allow displaying featured projects from the Content table
                // A migration should be created later to add the 'featured' column to the projects table
            }
            
            DB::commit();
            
            return redirect()->route('admin.content.featured-projects')
                ->with('success', 'Featured projects updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update featured projects: ' . $e->getMessage());
        }
    }

    /**
     * Display the hero section management page.
     */
    public function heroIndex()
    {
        $heroContent = Content::getBySection('hero');
        
        return Inertia::render('Admin/Content/Hero', [
            'heroContent' => $heroContent,
        ]);
    }

    /**
     * Update hero section content.
     */
    public function updateHero(Request $request)
    {
        $validated = $request->validate([
            'headline' => 'required|string|max:255',
            'subheading' => 'required|string|max:500',
            'cta_primary_text' => 'required|string|max:50',
            'cta_primary_url' => 'required|url|max:2000',
            'cta_secondary_text' => 'nullable|string|max:50',
            'cta_secondary_url' => 'nullable|url|max:2000',
            'background_image' => 'nullable|url|max:2000',
        ]);
        
        $content = Content::updateOrCreate(
            ['section' => 'hero'],
            [
                'title' => $validated['headline'],
                'subtitle' => $validated['subheading'],
                'content' => json_encode([
                    'headline' => $validated['headline'],
                    'subheading' => $validated['subheading'],
                    'cta_primary_text' => $validated['cta_primary_text'],
                    'cta_primary_url' => $validated['cta_primary_url'],
                    'cta_secondary_text' => $validated['cta_secondary_text'],
                    'cta_secondary_url' => $validated['cta_secondary_url'],
                ]),
                'image' => $validated['background_image'],
                'is_active' => true,
                'order' => 1,
            ]
        );
        
        return redirect()->route('admin.content.hero')
            ->with('success', 'Hero section updated successfully.');
    }
}