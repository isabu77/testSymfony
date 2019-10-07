<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="L'email existe déjà")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "L'adresse mail doit avoir au moins {{ limit }} caractères",
     *      maxMessage = "L'adresse mail doit avoir au plus {{ limit }} caractères"
     * )
     * @Assert\Email(message = "Merci d'entrer une adresse email valide")
     * @Assert\NotBlank(message = "Merci d'entrer une adresse email")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Le nom doit avoir au moins {{ limit }} caractères",
     *      maxMessage = "Le nom doit avoir au plus {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message = "Merci d'entrer un nom")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 8,
     *     minMessage = "Le mot de passe doit avoir au moins {{ limit }} caractères",
     * )
     * @Assert\EqualTo(
     *      propertyPath = "confirm_password",
     *      message = "Les 2 mots de passe doivent être identiques",
     * )
     * @Assert\NotBlank(message = "Merci d'entrer un mot de passe")
     */
    private $password;

    /**
     * @Assert\EqualTo(
     *      propertyPath = "password",
     *      message = "Les 2 mots de passe doivent être identiques",
     * )
     * @Assert\NotBlank(message = "Merci d'entrer un mot de passe")
     */
    public $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles(){
        return ['ROLE_ADMIN'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(){

    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(){

    }
}
