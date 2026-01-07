<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperIpAddress
 */
class IpAddress extends Model
{
    protected function casts(): array
    {
        return [
            'whitelisted' => 'boolean',
        ];
    }

    public function getSuspiciousAttribute(): bool
    {
        return $this->usage_type === 'Data Center/Web Hosting/Transit'
            || $this->abuse_score > 0
            || $this->report_count > 0;
    }
}
