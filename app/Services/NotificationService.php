<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Circulation;
use App\Models\AssetDisposal;
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

    // ==================== 🔥 CREATE NOTIFICATION UNTUK PENGAJUAN ASET ====================
    public function createDisposalNotification($userId, $disposalId, $title, $message, $type = 'info')
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'circulation_id' => null,
                'disposal_id' => $disposalId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'status' => 'unread',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create disposal notification: ' . $e->getMessage());
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

    // ==================== 🔥 SEND NOTIFICATION FOR ASSET DISPOSAL ====================
    /**
     * Kirim notifikasi soal pengajuan penghapusan aset.
     *
     * $action:
     * - 'pending'  → kirim ke SEMUA Super Admin (ada pengajuan baru menunggu konfirmasi)
     * - 'approved' → kirim ke User yang mengajukan (pengajuan disetujui, barang dihapus)
     * - 'rejected' → kirim ke User yang mengajukan (pengajuan ditolak)
     */
    public function sendDisposalNotification(AssetDisposal $disposal, $action)
    {
        $itemName = $disposal->item->name ?? 'Barang';

        if ($action === 'pending') {
            // Notif ke semua Super Admin
            $superAdmins = User::where('role', 'super_admin')->get();

            foreach ($superAdmins as $admin) {
                $this->createDisposalNotification(
                    $admin->id,
                    $disposal->id,
                    'Pengajuan Penghapusan Aset Baru',
                    ($disposal->user->name ?? 'User') . ' mengajukan penghapusan barang "' . $itemName . '" karena: ' . $disposal->reason,
                    'disposal_pending'
                );
            }
            return;
        }

        if ($action === 'approved') {
            $this->createDisposalNotification(
                $disposal->user_id,
                $disposal->id,
                'Pengajuan Penghapusan Disetujui',
                'Pengajuan penghapusan barang "' . $itemName . '" telah disetujui dan barang sudah dihapus dari daftar aset.',
                'disposal_approved'
            );

            // 🔥 Notif monitoring balik ke semua Super Admin (termasuk yang approve)
            $superAdmins = User::where('role', 'super_admin')->get();
            foreach ($superAdmins as $admin) {
                $this->createDisposalNotification(
                    $admin->id,
                    $disposal->id,
                    'Aset Dihapus',
                    'Barang "' . $itemName . '" telah dihapus dari sistem karena rusak (disetujui oleh ' . (auth()->user()->name ?? 'Super Admin') . ').',
                    'disposal_info'
                );
            }
            return;
        }

        if ($action === 'rejected') {
            $this->createDisposalNotification(
                $disposal->user_id,
                $disposal->id,
                'Pengajuan Penghapusan Ditolak',
                'Pengajuan penghapusan barang "' . $itemName . '" ditolak. Alasan: ' . ($disposal->rejection_reason ?? '-'),
                'disposal_rejected'
            );
            return;
        }
    }

    // ==================== GET UNREAD NOTIFICATIONS ====================
    public function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->with(['circulation.item', 'disposal.item'])
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