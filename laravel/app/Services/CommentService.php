<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entities\Comment;
use App\Entities\Post;
use App\Entities\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommentService
{
    private EntityManagerInterface $em;
    private $commentRepository;
    private $postRepository;
    private $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->commentRepository = $em->getRepository(Comment::class);
        $this->postRepository = $em->getRepository(Post::class);
        $this->userRepository = $em->getRepository(User::class);
    }

    public function getAllComments()
    {
        return $this->commentRepository->findAllComments();
    }

    public function getComment($id)
    {
        return $this->commentRepository->find($id);
    }

    public function createComment(array $data): Comment
    {
        // Очікуємо ключі: content, post_id, user_id
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'post_id' => 'required|integer',
            'user_id' => 'required|integer',
        ], [
            'content.required' => 'Content is required.',
            'post_id.required'  => 'Post ID is required.',
            'user_id.required'  => 'User ID is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $post = $this->postRepository->find($data['post_id']);
        if (!$post) {
            throw new \Exception('Post not found.');
        }

        $user = $this->userRepository->find($data['user_id']);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        $comment = new Comment();
        $comment->setContent($data['content']);
        $comment->setPost($post);
        $comment->setUser($user);
        $now = new \DateTime();
        $comment->setCreatedAt($now);
        $comment->setUpdatedAt($now);

        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    public function updateComment($id, array $data): ?Comment
    {
        $comment = $this->commentRepository->find($id);
        if (!$comment) {
            return null;
        }

        $rules = [
            'content' => 'sometimes|required|string',
            'post_id' => 'sometimes|required|integer',
            'user_id' => 'sometimes|required|integer',
        ];

        $validator = Validator::make($data, $rules, [
            'content.required' => 'Content is required.',
            'post_id.required'  => 'Post ID is required.',
            'user_id.required'  => 'User ID is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['content'])) {
            $comment->setContent($data['content']);
        }

        if (isset($data['post_id'])) {
            $post = $this->postRepository->find($data['post_id']);
            if (!$post) {
                throw new \Exception('Post not found.');
            }
            $comment->setPost($post);
        }

        if (isset($data['user_id'])) {
            $user = $this->userRepository->find($data['user_id']);
            if (!$user) {
                throw new \Exception('User not found.');
            }
            $comment->setUser($user);
        }

        $comment->setUpdatedAt(new \DateTime());
        $this->em->flush();

        return $comment;
    }

    public function deleteComment($id): bool
    {
        $comment = $this->commentRepository->find($id);
        if (!$comment) {
            return false;
        }
        $this->em->remove($comment);
        $this->em->flush();
        return true;
    }
}
