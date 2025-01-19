<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Classes\Enum\FileType;

/**
 * Class Files
 *
 * @property int $id
 * @property int $file_type
 * @property string $instance_key
 * @property string $instance_type
 * @property int $instance_id
 * @property string $file_path
 * @property string $file_name
 * @property int $user_id
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Files extends Model
{
    const INSTANCE_TYPE_CATEGORY = 1;
    const INSTANCE_TYPE_PRODUCT = 2;
    const INSTANCE_TYPE_VENDORS = 3;
    const INSTANCE_TYPE_LMS_USER = 4;
    const INSTANCE_TYPE_SETTING = 5;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $instanceTypes = [
        self::INSTANCE_TYPE_CATEGORY => 'category',
        self::INSTANCE_TYPE_PRODUCT => 'product',
        self::INSTANCE_TYPE_VENDORS => 'vendor',
    ];

    public static $statusOptions = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active'
    ];

    protected $fillable = [
        'file_type',
        'instance_key',
        'instance_type',
        'instance_id',
        'file_path',
        'file_name',
        'user_id',
        'status'
    ];

    /**
     * @return string
     */
    public function getFileSrc()
    {
        return '/' . $this->getBasePath() . base64_encode($this->file_path . $this->file_name);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        $basePath = '';
        if ($this->file_type == FileType::FILE_TYPE_IMAGE) {
            $basePath .= 'image/';
        } else if ($this->file_type == FileType::FILE_TYPE_VIDEO) {
            $basePath .= 'video/';
        }

        if ($connectionId = getSetting('connection_id')) {
            $basePath .= $connectionId . '/';
        }

        return $basePath;
    }
}
