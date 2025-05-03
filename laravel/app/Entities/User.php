<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use LaravelDoctrine\ORM\Auth\Authenticatable as DoctrineAuthenticatable;

#[ORM\Entity(repositoryClass: \App\Repositories\UserRepository::class)]
#[ORM\Table(name: "users")]
class User implements \JsonSerializable, Authenticatable, JWTSubject
{
    use DoctrineAuthenticatable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    protected string $password;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(name: 'remember_token', type: 'string', length: 100, nullable: true)]
    protected string $rememberToken;

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function setRememberToken($value): void
    {
        $this->rememberToken = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id'             => $this->getId(),
            'username'       => $this->getUsername(),
            'email'          => $this->getEmail(),
            'createdAt'      => $this->getCreatedAt()?->format('Y-m-d H:i:s'),
            'profilePicture' => $this->getProfilePicture(),
            'roles'          => $this->getRoles(),
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getAuthIdentifier();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'roles'    => $this->roles,
            'username' => $this->username,
            'email'    => $this->email,
        ];
    }
}
