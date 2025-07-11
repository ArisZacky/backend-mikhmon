<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use RouterOS\Exceptions\ClientException;
use App\Models\Router;
use App\Models\PaketVoucher;
use App\Models\UserList;
use Exception;

class MikrotikService
{
    protected Client $client;

    public function __construct($host, $username, $password, $port = 8728)
    {
        try {
            $this->client = new Client([
                'host' => $host,
                'user' => $username,
                'pass' => $password,
                'port' => $port,
                'timeout' => 10,
            ]);
        } catch (ClientException $e) {
            throw new Exception("Failed to connect to Mikrotik: " . $e->getMessage());
        } catch (\Throwable $e) {
            throw new Exception("Router connection error: " . $e->getMessage());
        }
    }

    // Get all Hotspot Users List
    public function getHotspotUsers(): array
    {
        $query = new Query('/ip/hotspot/user/print');
        return $this->client->query($query)->read();
    }

    // Get all Hotspot Profiles List (Paket Voucher)
    public function getHotspotProfiles(): array
    {
        $query = new Query('/ip/hotspot/user/profile/print');
        return $this->client->query($query)->read();
    }

    // Get all Hotspot Active Users List
    public function getHotspotActive(): array
    {
        $query = new Query('/ip/hotspot/active/print');
        return $this->client->query($query)->read();
    }
    
    // Syncronize Voucher yang diGenerate di masing-masing Router
    public function syncUserList(Router $router, int $userId): int
    {
        $users = $this->getHotspotUsers();

        $activeUsernames = $this->getActiveUsernames();

        $synced = 0;

        foreach ($users as $user) {

            $username = $user['name'] ?? '';

            // Skip username "default"
            if (str_contains($username, 'default')) {
                continue;
            }

            $profileName = $user['profile'] ?? null;

            // Check used via uptime
            $uptime = $user['uptime'] ?? null;

            $paket = PaketVoucher::where('router_id', $router->id)
                ->where('voucher_name', $profileName)
                ->first();
            
            $isActive = in_array($username, $activeUsernames);
            // Check if user has been used (has uptime or currently active)
            $isUsed = (!empty($uptime) && $uptime !== '0s' && $uptime !== '00:00:00') || $isActive;

            // Ambil record lama jika ada
            $existing = UserList::where('user', $username)
                ->where('paket_voucher_id', $paket?->id)
                ->first();

            // Kasi nilai ke used_at
            $usedAt = null;
            // Kalo isUsed = true dan existing record belum pernah digunakan (used_at = null)
            if ($isUsed && ($existing === null || $existing->used_at === null)) {
                $usedAt = now();
            } else {
                $usedAt = $existing?->used_at;
            }

            $record = UserList::updateOrCreate(
                [
                    'user' => $username,
                    'paket_voucher_id' => $paket?->id,
                ],
                [
                    'user_password' => $user['password'] ?? null,
                    'profile' => $profileName,
                    'comment' => $user['comment'] ?? null,
                    'uptime' => $uptime,
                    'used_at' => $usedAt,
                    'is_used' => $isUsed,
                    'is_active' => $isActive,
                    'user_id' => $userId,
                ]
            );

            $synced++;
        }
        return $synced;
    }

    public function syncAllRoutersUserList()
    {
        // $routers = Router::all();
        // $syncedCount = 0;

        // foreach ($routers as $router) {
        //     // Initiate Mikrotik Service
        //     $service = new MikrotikService(
        //         $router->ip_mikrotik,
        //         $router->user_mikrotik,
        //         $router->password_mikrotik,
        //         8728
        //     );
            
        //     $user_id = $router->user_id;
        //     $syncedCount += $this->syncUserList($router, $user_id);
        // }

        // return $syncedCount;
    }

    public function getActiveUsernames(): array
    {
        return collect($this->getHotspotActive())->pluck('user')->toArray();
    }
}