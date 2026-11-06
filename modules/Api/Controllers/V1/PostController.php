<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;

class PostController extends BaseApiController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /api/v1/posts?page=&lang=
     * Schema: IPost[]
     *
     * Note: Jootball ยังไม่มี posts/news table
     * เมื่อสร้างตาราง posts แล้วให้มาอัปเดต controller นี้
     */
    public function index()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $posts = $this->db->table('posts')
            ->orderBy('date', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        $result = array_map(function ($p) {
            return [
                'id' => (int) $p['id'],
                'title' => $p['title'] ?? '',
                'description' => $p['description'] ?? '',
                'image' => isset($p['image']) ? $this->imageUrl($p['image']) : null,
                'categoryName' => $p['category_name'] ?? null,
                'location' => $p['location'] ?? null,
                'videoUrl' => $p['video_url'] ?? null,
                'creator' => $p['creator'] ?? null,
                'date' => isset($p['date']) ? date('c', strtotime($p['date'])) : null,
            ];
        }, $posts);

        return $this->successResponse($result);
    }

    /**
     * GET /api/v1/posts/:id
     * Schema: IPost
     */
    public function show($id = null)
    {
        $post = $this->db->table('posts')->where('id', $id)->get()->getRowArray();

        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        return $this->successResponse([
            'id' => (int) $post['id'],
            'title' => $post['title'] ?? '',
            'description' => $post['description'] ?? '',
            'image' => isset($post['image']) ? $this->imageUrl($post['image']) : null,
            'categoryName' => $post['category_name'] ?? null,
            'location' => $post['location'] ?? null,
            'videoUrl' => $post['video_url'] ?? null,
            'creator' => $post['creator'] ?? null,
            'date' => isset($post['date']) ? date('c', strtotime($post['date'])) : null,
        ]);
    }
}
