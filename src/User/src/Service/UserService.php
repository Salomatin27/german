<?php
namespace User\Service;

use App\Entity\Lng;
use App\Entity\Role;
use App\Entity\Surgeon;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Math\Rand;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserService
{
    /**
     * Doctrine entity manager.
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function getLanguageDefault()
    {
        $lng = $this->entityManager->getRepository(Lng::class)->findAll()[0];
        if (!$lng) {
            throw new \Exception('The default language don\'t set at database');
        }

        return $lng;
    }

    /**
     * This method adds a new user.
     */
    public function addUser($data)
    {
        $error = null;
        try {
            // Do not allow several users with the same email address.
            if ($this->checkUserExists($data['email'])) {
                throw new \Exception("User with email address " . $data['$email'] . " already exists");
            }

            // Create new User entity.
            $user = new User();
            $lng = $this->getLanguageDefault();
            $user->setLng($lng);
            $user->setEmail($data['email']);
            $user->setFullName($data['full_name']);
            /** @var Role $role */
            $role = $this->entityManager->getRepository(Role::class)->find($data['role']);
            if ($role === null) {
                throw new \Exception("Роль не установлена");
            }
            $user->setRole($role);

            /** @var Surgeon $surgeon */
            $surgeon = null;
            if ($data['surgeon']) {
                $surgeon = $this->entityManager->getRepository(Surgeon::class)->find($data['surgeon']);
            }
            $user->setSurgeon($surgeon);

            // Encrypt password and store the password in encrypted state.
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create($data['password']);
            $user->setPassword($passwordHash);

            $user->setStatus($data['status']);

            $currentDate = date_create(date('Y-m-d H:i:s'));
            $user->setDateCreated($currentDate);

            //$user->setPhoneIn($data['phoneIn']);
            $user->setPhoneOut($data['phoneOut']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($user);

            // Apply changes to database.
            $this->entityManager->flush($user);
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return (object)['error' => $error, 'user' => $user];
    }

    /**
     * This method updates data of an existing user.
     * @param User $user
     * @param array $data
     * @throws
     *
     * @return User $user
     */
    public function updateUser(User $user, $data): \stdClass
    {
        $error = null;
        try {
            // Do not allow to change user email if another user with such email already exits.
            if ($user->getEmail()!=$data['email'] && $this->checkUserExists($data['email'])) {
                throw new \Exception("Another user with email address " . $data['email'] . " already exists");
            }

            $lng = $this->getLanguageDefault();
            $user->setLng($lng);
            $user->setEmail($data['email']);
            $user->setFullName($data['full_name']);
            $user->setStatus($data['status']);
            //$user->setPhoneIn($data['phoneIn']);
            $user->setPhoneOut($data['phoneOut']);

            /** @var Surgeon $surgeon */
            $surgeon = null;
            if ($data['surgeon']) {
                $surgeon = $this->entityManager->getRepository(Surgeon::class)->find($data['surgeon']);
            }
            $user->setSurgeon($surgeon);

            /** @var Role $role */
            $role = $this->entityManager->getRepository(Role::class)->find($data['role']);
            if ($role === null) {
                throw new \Exception("Роль не установлена");
            }
            $user->setRole($role);
            // Apply changes to database.
            $this->entityManager->flush($user);
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return (object)['error' => $error, 'user' => $user];
    }

    /**
     * Checks whether an active user with given email address already exists in the database.
     */
    public function checkUserExists($email)
    {

        $user = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($email);

        return $user !== null;
    }

    /**
     * Checks that the given password is correct.
     */
    public function validatePassword($user, $password)
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }

        return false;
    }

    /**
     * Generates a password reset token for the user. This token is then stored in database and
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is
     * directed to the Set Password page.
     */
    public function generatePasswordResetToken($user)
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz');
        $user->setPasswordResetToken($token);

        $currentDate = date_create(date('Y-m-d H:i:s'));
        $user->setPasswordResetTokenCreationDate($currentDate);

        $this->entityManager->flush();

        $subject = 'Password Reset';

        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password?token=' . $token;

        $body = 'Please follow the link below to reset your password:\n';
        $body .= "$passwordResetUrl\n";
        $body .= "If you haven't asked to reset your password, please ignore this message.\n";

        // Send email to user.
        mail($user->getEmail(), $subject, $body);
    }

    /**
     * Checks whether the given password reset token is a valid one.
     */
    public function validatePasswordResetToken($passwordResetToken)
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneByPasswordResetToken($passwordResetToken);

        if ($user==null) {
            return false;
        }

        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = strtotime($tokenCreationDate);

        $currentDate = strtotime('now');

        if ($currentDate - $tokenCreationDate > 24*60*60) {
            return false; // expired
        }

        return true;
    }

    /**
     * This method sets new password by password reset token.
     */
    public function setNewPasswordByToken($passwordResetToken, $newPassword)
    {
        if (!$this->validatePasswordResetToken($passwordResetToken)) {
            return false;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->findOneByPasswordResetToken($passwordResetToken);

        if ($user==null) {
            return false;
        }

        // Set new password for user
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);

        // Remove password reset token
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);

        $this->entityManager->flush();

        return true;
    }

    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     */
    public function changePassword($user, $data)
    {
//        $oldPassword = $data['old_password'];

        // Check that old password is correct
//        if (!$this->validatePassword($user, $oldPassword)) {
//            return false;
//        }

        $newPassword = $data['new_password'];

        // Check password length
        if (strlen($newPassword)<6 || strlen($newPassword)>64) {
            return false;
        }

        // Set new password for user
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);

        // Apply changes
        $this->entityManager->flush($user);

        return true;
    }

    /** Get Users
     * @return User[] $users
     */
    public function getUsers()
    {
        $users = $this->entityManager->getRepository(User::class)
            ->findBy([], ['userId'=>'DESC']);
        return $users;
    }

    public function findUserByEmail($email)
    {
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['email'=>$email]);
    }
}
