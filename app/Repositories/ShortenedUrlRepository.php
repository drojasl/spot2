<?php

namespace App\Repositories;

use App\Models\ShortenedUrl;

class ShortenedUrlRepository implements ShortenedUrlRepositoryInterface
{
    public function create(array $data): ShortenedUrl
    {
        return ShortenedUrl::create($data);
    }

    public function findByUserId($userId, $perPage=10, $page=1)
    {
        return ShortenedUrl::where('user_id', $userId)->paginate($perPage, ['*'], 'page', $page);
    }

    public function delete($id, $userId)
    {
        $shortenedUrl = ShortenedUrl::where('id', $id)->where('user_id', $userId)->first();

        if ($shortenedUrl) {
            $shortenedUrl->delete();
            return true;
        } else {
            return false;
        }
    }

    public function findByShortened($shortened)
    {
        return ShortenedUrl::where('shortened', $shortened)->first();
    }
}
