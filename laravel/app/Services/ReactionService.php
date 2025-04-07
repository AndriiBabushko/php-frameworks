<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entities\Reaction;
use App\Entities\Post;
use App\Entities\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReactionService
{
    private EntityManagerInterface $em;
    private $reactionRepository;
    private $postRepository;
    private $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->reactionRepository = $em->getRepository(Reaction::class);
        $this->postRepository = $em->getRepository(Post::class);
        $this->userRepository = $em->getRepository(User::class);
    }

    public function getAllReactions()
    {
        return $this->reactionRepository->findAllReactions();
    }

    public function getReaction($id)
    {
        return $this->reactionRepository->find($id);
    }

    public function createReaction(array $data): Reaction
    {
        // Очікуються поля: type, post_id, user_id
        $validator = Validator::make($data, [
            'type'    => 'required|string|max:20',
            'post_id' => 'required|integer',
            'user_id' => 'required|integer',
        ], [
            'type.required'    => 'Type is required.',
            'type.max'         => 'Type cannot exceed 20 characters.',
            'post_id.required' => 'Post ID is required.',
            'user_id.required' => 'User ID is required.',
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

        $reaction = new Reaction();
        $reaction->setType($data['type']);
        $reaction->setPost($post);
        $reaction->setUser($user);
        $now = new \DateTime();
        $reaction->setCreatedAt($now);
        $reaction->setUpdatedAt($now);

        $this->em->persist($reaction);
        $this->em->flush();

        return $reaction;
    }

    public function updateReaction($id, array $data): ?Reaction
    {
        $reaction = $this->reactionRepository->find($id);
        if (!$reaction) {
            return null;
        }

        $rules = [
            'type'    => 'sometimes|required|string|max:20',
            'post_id' => 'sometimes|required|integer',
            'user_id' => 'sometimes|required|integer',
        ];

        $validator = Validator::make($data, $rules, [
            'type.required'    => 'Type is required.',
            'type.max'         => 'Type cannot exceed 20 characters.',
            'post_id.required' => 'Post ID is required.',
            'user_id.required' => 'User ID is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['type'])) {
            $reaction->setType($data['type']);
        }

        if (isset($data['post_id'])) {
            $post = $this->postRepository->find($data['post_id']);
            if (!$post) {
                throw new \Exception('Post not found.');
            }
            $reaction->setPost($post);
        }

        if (isset($data['user_id'])) {
            $user = $this->userRepository->find($data['user_id']);
            if (!$user) {
                throw new \Exception('User not found.');
            }
            $reaction->setUser($user);
        }

        $reaction->setUpdatedAt(new \DateTime());
        $this->em->flush();
        return $reaction;
    }

    public function deleteReaction($id): bool
    {
        $reaction = $this->reactionRepository->find($id);
        if (!$reaction) {
            return false;
        }
        $this->em->remove($reaction);
        $this->em->flush();
        return true;
    }
}
