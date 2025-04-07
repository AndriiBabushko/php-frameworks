<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entities\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserService
{
    private EntityManagerInterface $em;
    private $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->userRepository = $em->getRepository(User::class);
    }

    public function getAllUsers(array $params = []): array
    {
        $itemsPerPage = isset($params['itemsPerPage']) ? (int)$params['itemsPerPage'] : 10;
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        return $this->userRepository->getAllUsersByFilter($params, $itemsPerPage, $page);
    }

    public function getUser($id)
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data): User
    {
        $validator = Validator::make($data, [
            'username'       => 'required|string|min:3|max:50',
            'email'          => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique(\App\Entities\User::class, 'email')
            ],
            'password'       => 'required|string|min:8|max:255',
            'profilePicture' => 'nullable|url|max:255',
        ], [
            'username.required' => 'Username should not be blank.',
            'username.min'      => 'Username must be at least 3 characters long.',
            'username.max'      => 'Username cannot exceed 50 characters.',
            'email.required'    => 'Email should not be blank.',
            'email.email'       => 'The email is not a valid email.',
            'email.max'         => 'Email cannot exceed 100 characters.',
            'email.unique'      => 'Email must be unique.',
            'password.required' => 'Password should not be blank.',
            'password.min'      => 'Password must be at least 8 characters long.',
            'password.max'      => 'Password cannot exceed 255 characters.',
            'profilePicture.url'=> 'ProfilePicture must be a valid URL.',
            'profilePicture.max'=> 'ProfilePicture cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword(Hash::make($data['password']));
        $user->setCreatedAt(new \DateTime());
        if (isset($data['profilePicture'])) {
            $user->setProfilePicture($data['profilePicture']);
        }

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function updateUser($id, array $data): ?User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return null;
        }

        $rules = [
            'username'       => 'sometimes|required|string|min:3|max:50',
            'email'          => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique(\App\Entities\User::class, 'email')->ignore($id)
            ],
            'password'       => 'sometimes|required|string|min:8|max:255',
            'profilePicture' => 'nullable|url|max:255',
        ];

        $validator = Validator::make($data, $rules, [
            'username.required' => 'Username should not be blank.',
            'username.min'      => 'Username must be at least 3 characters long.',
            'username.max'      => 'Username cannot exceed 50 characters.',
            'email.required'    => 'Email should not be blank.',
            'email.email'       => 'The email is not a valid email.',
            'email.max'         => 'Email cannot exceed 100 characters.',
            'email.unique'      => 'Email must be unique.',
            'password.required' => 'Password should not be blank.',
            'password.min'      => 'Password must be at least 8 characters long.',
            'password.max'      => 'Password cannot exceed 255 characters.',
            'profilePicture.url'=> 'ProfilePicture must be a valid URL.',
            'profilePicture.max'=> 'ProfilePicture cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword(Hash::make($data['password']));
        }
        if (array_key_exists('profilePicture', $data)) {
            $user->setProfilePicture($data['profilePicture']);
        }

        $this->em->flush();
        return $user;
    }

    public function deleteUser($id): bool
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return false;
        }
        $this->em->remove($user);
        $this->em->flush();
        return true;
    }
}
