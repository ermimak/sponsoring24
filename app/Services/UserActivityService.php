<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserActivityService
{
    /**
     * Log user activity
     *
     * @param string $activityType
     * @param string $description
     * @param array $metadata
     * @param int|null $userId
     * @return UserActivity
     */
    public static function log($activityType, $description, $metadata = [], $userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return null; // Don't log if no user is associated
        }
        
        return UserActivity::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata
        ]);
    }
    
    /**
     * Log authentication activity
     *
     * @param string $action
     * @param int|null $userId
     * @param array $metadata
     * @return UserActivity
     */
    public static function logAuth($action, $userId = null, $metadata = [])
    {
        return self::log('authentication', $action, $metadata, $userId);
    }
    
    /**
     * Log project activity
     *
     * @param string $action
     * @param int|null $userId
     * @param array $metadata
     * @return UserActivity
     */
    public static function logProject($action, $userId = null, $metadata = [])
    {
        return self::log('project', $action, $metadata, $userId);
    }
    
    /**
     * Log payment activity
     *
     * @param string $action
     * @param int|null $userId
     * @param array $metadata
     * @return UserActivity
     */
    public static function logPayment($action, $userId = null, $metadata = [])
    {
        return self::log('payment', $action, $metadata, $userId);
    }
    
    /**
     * Log profile update activity
     *
     * @param string $action
     * @param array $metadata
     * @param int|null $userId
     * @return UserActivity
     */
    public static function logProfileUpdate($action, $metadata = [], $userId = null)
    {
        return self::log('profile', $action, $metadata, $userId);
    }
    

    
    /**
     * Log admin activity
     *
     * @param string $action
     * @param array $metadata
     * @param int|null $userId
     * @return UserActivity
     */
    public static function logAdmin($action, $metadata = [], $userId = null)
    {
        return self::log('admin', $action, $metadata, $userId);
    }
}
