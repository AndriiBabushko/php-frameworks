<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entities\Notification;
use App\Entities\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NotificationService
{
    private EntityManagerInterface $em;
    private $notificationRepository;
    private $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->notificationRepository = $em->getRepository(Notification::class);
        $this->userRepository = $em->getRepository(User::class);
    }

    public function getAllNotifications(array $params = []): array
    {
        $itemsPerPage = isset($params['itemsPerPage']) ? (int)$params['itemsPerPage'] : 10;
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        return $this->notificationRepository->getAllNotificationsByFilter($params, $itemsPerPage, $page);
    }

    public function getNotification($id)
    {
        return $this->notificationRepository->find($id);
    }

    public function createNotification(array $data): Notification
    {
        $validator = Validator::make($data, [
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'user_id' => 'required|integer',
        ], [
            'title.required'   => 'Title is required.',
            'message.required' => 'Message is required.',
            'user_id.required' => 'User ID is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = $this->userRepository->find($data['user_id']);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        $notification = new Notification();
        $notification->setTitle($data['title']);
        $notification->setMessage($data['message']);
        $notification->setUser($user);
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTime());

        $this->em->persist($notification);
        $this->em->flush();
        return $notification;
    }

    public function updateNotification($id, array $data): ?Notification
    {
        $notification = $this->notificationRepository->find($id);
        if (!$notification) {
            return null;
        }

        $rules = [
            'title'   => 'sometimes|required|string|max:255',
            'message' => 'sometimes|required|string',
            'is_read' => 'sometimes|required|boolean',
            'user_id' => 'sometimes|required|integer',
        ];

        $validator = Validator::make($data, $rules, [
            'title.required'   => 'Title is required.',
            'message.required' => 'Message is required.',
            'is_read.required' => 'is_read is required.',
            'user_id.required' => 'User ID is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['title'])) {
            $notification->setTitle($data['title']);
        }
        if (isset($data['message'])) {
            $notification->setMessage($data['message']);
        }
        if (isset($data['is_read'])) {
            $notification->setIsRead($data['is_read']);
        }
        if (isset($data['user_id'])) {
            $user = $this->userRepository->find($data['user_id']);
            if (!$user) {
                throw new \Exception('User not found.');
            }
            $notification->setUser($user);
        }

        $this->em->flush();
        return $notification;
    }

    public function deleteNotification($id): bool
    {
        $notification = $this->notificationRepository->find($id);
        if (!$notification) {
            return false;
        }
        $this->em->remove($notification);
        $this->em->flush();
        return true;
    }
}
