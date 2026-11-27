<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Administrateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:administrateur:changer-mot-de-passe', description: 'Change le mot de passe d’un compte administrateur')]
final class ChangerMotDePasseCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Adresse e-mail du compte');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = mb_strtolower(trim((string) $input->getArgument('email')));

        $administrateur = $this->entityManager->getRepository(Administrateur::class)->findOneBy(['email' => $email]);
        if ($administrateur === null) {
            $io->error('Aucun compte pour cette adresse.');

            return Command::FAILURE;
        }

        $motDePasse = (string) $io->askHidden('Nouveau mot de passe (12 caractères minimum)');
        if (mb_strlen($motDePasse) < 12) {
            $io->error('Mot de passe trop court : 12 caractères minimum.');

            return Command::FAILURE;
        }

        $confirmation = (string) $io->askHidden('Confirmez le mot de passe');
        if ($motDePasse !== $confirmation) {
            $io->error('Les mots de passe ne correspondent pas.');

            return Command::FAILURE;
        }

        $administrateur->setPassword($this->hasher->hashPassword($administrateur, $motDePasse));
        $this->entityManager->flush();

        $io->success('Mot de passe changé pour ' . $email . '.');

        return Command::SUCCESS;
    }
}
