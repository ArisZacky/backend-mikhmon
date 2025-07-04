<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use App\Models\Router;
use App\Models\PaketVoucher;
use App\Models\UserList;

class MikrotikService
{
    protected Client $client;

    public function __construct($host, $username, $password, $port = 8728)
    {
        $this->client = new Client([
            'host' => $host,
            'user' => $username,
            'pass' => $password,
            'port' => $port,
            'timeout' => 10,
        ]);
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
    
    // Syncronize Voucher yang diGenerate
    public function syncUserList(Router $router, int $userId): int
    {
        $users = $this->getHotspotUsers();
        $activeUsers = $this->getHotspotActive();

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

            $record = UserList::updateOrCreate(
                [
                    'user' => $username,
                    'paket_voucher_id' => $paket?->id,
                ],
                [
                    'password' => $user['password'] ?? null,
                    'profile' => $profileName,
                    'comment' => $user['comment'] ?? null,
                    'uptime' => $uptime,
                    'is_used' => $isUsed,
                    'is_active' => $isActive,
                    'user_id' => $userId,
                ]
            );

            $synced++;
        }
        return $synced;
    }

    public function getActiveUsernames(): array
    {
        return collect($this->getHotspotActive())->pluck('user')->toArray();
    }
}