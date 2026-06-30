<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Circulation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    // ==================== CREATE NOTIFICATION ====================
    public function create($userId, $circulationId, $title, $message, $type = 'info')
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'circulation_id' => $circulationId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'status' => 'unread',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
            return null;
        }
    }

    // ==================== SEND NOTIFICATION FOR CIRCULATION ====================
    public function sendCirculationNotification(Circulation $circulation, $action)
    {
        $unitId = $circulation->item->unit_id;
        $adminUsers = User::where('role', 'admin_unit')
            ->where('unit_id', $unitId)
            ->get();

        $messages = [
            'pending' => [
                'title' => 'Peminjaman Baru',
                'message' => $circulation->borrower_name . ' mengajukan peminjaman ' . $circulation->item->name,
                'type' => 'pending'
            ],
            'return_pending' => [
                'title' => 'Pengembalian Diajukan',
                'message' => $circulation->borrower_name . ' mengajukan pengembalian ' . $circulation->item->name,
                'type' => 'return_pending'
            ],
            'approved' => [
                'title' => 'Peminjaman Disetujui',
                'message' => 'Peminjaman ' . $circulation->item->name . ' oleh ' . $circulation->borrower_name . ' telah disetujui',
                'type' => 'approved'
            ],
            'rejected' => [
                'title' => 'Peminjaman Ditolak',
                'message' => 'Peminjaman ' . $circulation->item->name . ' oleh ' . $circulation->borrower_name . ' telah ditolak',
                'type' => 'rejected'
            ],
            'returned' => [
                'title' => 'Pengembalian Dikonfirmasi',
                'message' => 'Pengembalian ' . $circulation->item->name . ' oleh ' . $circulation->borrower_name . ' telah dikonfirmasi',
                'type' => 'returned'
            ],
        ];

        if (!isset($messages[$action])) {
            return;
        }

        $data = $messages[$action];

        foreach ($adminUsers as $admin) {
            $this->create(
                $admin->id,
                $circulation->id,
                $data['title'],
                $data['message'],
                $data['type']
            );
        }
    }

    // ==================== GET UNREAD NOTIFICATIONS ====================
    public function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->with('circulation.item')
            ->latest()
            ->get();
    }

    // ==================== GET UNREAD COUNT ====================
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();
    }

    // ==================== MARK AS READ ====================
    public function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    // ==================== MARK ALL AS READ ====================
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->update([
                'status' => 'read',
                'read_at' => now()
            ]);
    }
}