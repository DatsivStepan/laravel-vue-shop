<?php

namespace App\Repositories\Admin;

use App\Classes\Enum\FileType;
use App\Models\Files;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;
use Image;

class FilesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Files::class;
    }

    /**
     * @param string $fileType
     * @param string $type
     * @param string $key
     * @param int $id
     * @param string $imagePath
     * @param string $imageName
     * @param null|int $userid
     * @return Model
     */
    public static function saveFile(
        string $fileType,
        string $type,
        string $key,
        int $id,
        string $imagePath,
        string $imageName,
        ?int $userid = null
    ) {
        return (new self)->create([
            'file_type' => $fileType,
            'instance_key' => $key,
            'instance_type' => $type,
            'instance_id' => $id,
            'file_path' => $imagePath,
            'file_name' => $imageName,
            'user_id' => $userid
        ]);
    }

    /**
     * @param string $type
     * @param string $key
     * @param int $instanceid
     * @param string $id
     * @param null|int $userid
     * @param array $notDeleteIds
     * @return bool
     */
    public static function deleteFile(string $type, string $key, int $instanceid, ?int $id = null, $notDeleteIds = [])
    {
        $options = [
            'filters' => ['id' => $id]
        ];
        if (!$id) {
            $options['filters'] = [
                'instance_type' => $type,
                'instance_key' => $key,
                'instance_id' => $instanceid
            ];
        }

        $rep = new self;
        if ($notDeleteIds) {
            $options['notInFilters'] = [
                'id' => $notDeleteIds
            ];
        }

        return $rep->deleteAllItems($options);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getInstanceType($type)
    {
        $instanceTypes = ($this->getModelClass())::$instanceTypes;
        return array_key_exists($type, $instanceTypes) ? $instanceTypes[$type] : 'other';
    }

    /**
     * @param int $objectId
     * @param int $type
     * @param string $key
     * @param null|File $videos
     */
    public function saveVideo($objectId, $type, $key = 'video', $videos = null)
    {
        if ($videos) {
            if (is_array($videos)) {
                $notDeleteIds = [];
                foreach ($videos as $video) {
                    if (strpos($video, 'data:') !== false) {
                        $videoName = time() . '.' . explode('/', explode(':', substr($video, 0, strpos($video, ';')))[1])[1];
                        $videoPath = 'video/' . $this->getInstanceType($type) . '/'. $objectId;
                        $video = substr($video, strpos($video, ',') + 1);
                        Storage::put($videoPath . $videoName, base64_decode($video));

                        $model = self::saveFile(FileType::FILE_TYPE_VIDEO, $type, $key, $objectId, $videoPath, $videoName);

                        $notDeleteIds[] = $model->id;
                    } else {
                        $model = new self;
                        $model = $model->getOneByConditions([
                            'instance_key' => $key,
                            'instance_type' => $type,
                            'instance_id' => $objectId,
                            "CONCAT('/', `file_path`, '', `file_name`)" => $video
                        ]);
                        if ($model) {
                            $notDeleteIds[] = $model->id;
                        }
                    }
                    self::deleteFile($type, $key, $objectId, null, $notDeleteIds);
                }
            }
        } else {
            self::deleteFile($type, $key, $objectId);
        }
    }

    /**
     * @param int $objectId
     * @param int $type
     * @param string $key
     * @param null|File $images
     *
     * @return array
     */
    public function saveImages($objectId, $type, $key = 'image', $images = null)
    {
        if ($images) {
            if (is_array($images)) {
                $notDeleteIds = [];
                foreach ($images as $image) {
                    if (strpos($image, 'data:') !== false) {
                        $imageName = time() . '.' . explode('/',
                        explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                        $imagePath = 'images/' . $this->getInstanceType($type) . '/' . $objectId;

                        $img = Image::make($image);
                        $img->stream();
                        Storage::put($imagePath . $imageName, $img);

                        $model = self::saveFile(FileType::FILE_TYPE_IMAGE, $type, $key, $objectId, $imagePath, $imageName);

                        $notDeleteIds[] = $model->id;
                    } else {
                        $model = new self;
                        $model = $model->getOneByConditions([
                            'instance_key' => $key,
                            'instance_type' => $type,
                            'instance_id' => $objectId,
                        ]);

                        if ($model) {
                            $notDeleteIds[] = $model->id;
                        }
                    }
                    self::deleteFile($type, $key, $objectId, null, $notDeleteIds);
                    return $notDeleteIds;
                }
            }
        } else {
            self::deleteFile($type, $key, $objectId);
        }
        return [];
    }
}
