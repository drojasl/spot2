<?php

namespace App\Repositories;

use App\Models\ShortenedUrl;

interface ShortenedUrlRepositoryInterface 
{
    public function create(array $data): ShortenedUrl;
    public function findByUserId($userId, $per_page, $page);
    public function delete($id, $userId);
    public function findByShortened($shortened);
}
