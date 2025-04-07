<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entities\Post;
use App\Entities\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostService
{
    private EntityManagerInterface $em;
    private $postRepository;
    private $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->postRepository = $em->getRepository(Post::class);
        $this->userRepository = $em->getRepository(User::class);
    }

    public function getAllPosts()
    {
        return $this->postRepository->findAllPosts();
    }

    public function getPost($id)
    {
        return $this->postRepository->find($id);
    }

    public function createPost(array $data): Post
    {
        $validator = Validator::make($data, [
            'title'     => 'required|string|min:3|max:150',
            'content'   => 'required|string',
            'imageUrl'  => 'nullable|url|max:255',
            'author_id' => 'required|integer'
        ], [
            'title.required'    => 'Title should not be blank.',
            'title.min'         => 'Title must be at least 3 characters long.',
            'title.max'         => 'Title cannot exceed 150 characters.',
            'content.required'  => 'Content should not be blank.',
            'imageUrl.url'      => 'Image URL must be a valid URL.',
            'imageUrl.max'      => 'Image URL cannot exceed 255 characters.',
            'author_id.required'=> 'Author ID is required.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $author = $this->userRepository->find($data['author_id']);
        if (!$author) {
            throw new \Exception('Author not found.');
        }

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setImageUrl($data['imageUrl'] ?? null);
        $post->setAuthor($author);
        $now = new \DateTime();
        $post->setCreatedAt($now);
        $post->setUpdatedAt($now);

        $this->em->persist($post);
        $this->em->flush();
        return $post;
    }

    public function updatePost($id, array $data): ?Post
    {
        $post = $this->postRepository->find($id);
        if (!$post) {
            return null;
        }

        $rules = [
            'title'     => 'sometimes|required|string|min:3|max:150',
            'content'   => 'sometimes|required|string',
            'imageUrl'  => 'nullable|url|max:255',
            'author_id' => 'sometimes|required|integer'
        ];

        $validator = Validator::make($data, $rules, [
            'title.required'    => 'Title should not be blank.',
            'title.min'         => 'Title must be at least 3 characters long.',
            'title.max'         => 'Title cannot exceed 150 characters.',
            'content.required'  => 'Content should not be blank.',
            'imageUrl.url'      => 'Image URL must be a valid URL.',
            'imageUrl.max'      => 'Image URL cannot exceed 255 characters.',
            'author_id.required'=> 'Author ID is required.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }
        if (array_key_exists('imageUrl', $data)) {
            $post->setImageUrl($data['imageUrl']);
        }
        if (isset($data['author_id'])) {
            $author = $this->userRepository->find($data['author_id']);
            if (!$author) {
                throw new \Exception('Author not found.');
            }
            $post->setAuthor($author);
        }
        $post->setUpdatedAt(new \DateTime());
        $this->em->flush();
        return $post;
    }

    public function deletePost($id): bool
    {
        $post = $this->postRepository->find($id);
        if (!$post) {
            return false;
        }
        $this->em->remove($post);
        $this->em->flush();
        return true;
    }
}
