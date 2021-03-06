<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\UserService;
use App\Entity\Role;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserService $us) {
        $this->validator = $validator;
        $this->em = $em;
        $this->encoder = $encoder;
        $this->us = $us;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new user via command line.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        
        $io->title("This process will ask you questions in order to create a new user.");

        $username = $io->ask('Username', null, function ($input) {
            if (empty($input)) {
                throw new \RuntimeException('Username must not be empty!');
            }
            if (!$this->us->isUsernameAvailable($input)) {
                throw new \RuntimeException('Username already exists!');
            }
            return $input;
        });
        $password = $io->ask('Password (min 8 characters)', null, function ($input) {
            if (strlen($input) < 8) {
                throw new \RuntimeException('Password must be at least 8 characters long!');
            }
            return $input;
        });
        $firstName = $io->ask('Firstname', null, function ($input) {
            if (empty($input)) {
                throw new \RuntimeException('Firstname must not be empty!');
            }
            return $input;
        });
        $lastName = $io->ask('Lastname', null, function ($input) {
            if (empty($input)) {
                throw new \RuntimeException('Lastname must not be empty!');
            }
            return $input;
        });
        $email = $io->ask('E-Mail', null, function ($input) {
            $emailConstraint = new Assert\Email();
            $errors = $this->validator->validate($input, $emailConstraint);
            if (empty($input) || count($errors) > 0) {
                throw new \RuntimeException('You must insert a valid mail address!');
            }
            return $input;
        });
        
        $question = new ChoiceQuestion(
            'Please select the user role',
            ['ADMIN', 'USER'],
            1
        );
        $role = $helper->ask($input, $output, $question);
        if($role === 'ADMIN') {
            $role = 1;
        } else {
            $role = 2;
        }
        $user = new User();
        $user->setEmail($email);
        $user->setFirstname($firstName);
        $user->setLastname($lastName);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setUsername($username);
        $user->setRole($this->em->getRepository(Role::class)->find($role));
        $user->setActive(true);
        $this->em->persist($user);
        
        $this->em->flush();
        
        $io->success('User created!');

        return Command::SUCCESS;
    }
}
