<?php

namespace App\Http\Controllers;

use App\Tenancy\Tenant;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Image;

class FileController extends Controller
{
    /**
     * @return Image
     */
    public function getImage($id, $url)
    {
        $url = base64_decode($url);

        /* switch database */
        if (!$tenant = Tenant::find($id)) {
            throw new ValidationException('Tenant does not exists');
        }

        tenancy()->initialize($tenant);

        $storagePath = Storage::get($url);

        return Image::make($storagePath)->response();
    }

    /**
     * @return \Image
     */
    public function getVideo($id, $url)
    {
        $url = base64_decode($url);

        /* switch database */
        if (!$tenant = Tenant::find($id)) {
            throw new ValidationException('Tenant does not exists');
        }

        tenancy()->initialize($tenant);

        return Response::make(Storage::get($url), 200, [
            'Content-Type' => 'video/*',
            'Content-Disposition' => 'inline; filename="' . $url . '"'
        ]);
    }

    /**
     * @return Response
     */
    public function getPdf($id, $url)
    {
        $url = base64_decode($url);

        /* switch database */
        if (!$tenant = Tenant::find($id)) {
            throw new ValidationException('Tenant does not exists');
        }

        tenancy()->initialize($tenant);

        return Response::make(Storage::get($url), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $url . '"'
        ]);
    }
}
