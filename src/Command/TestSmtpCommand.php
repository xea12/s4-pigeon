<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class TestSmtpCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:test-smtp');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $transport = new EsmtpTransport(
                'panel01.sprintdatacenter.pl',
                587,
                true
            );

            $transport->setUsername('utnij@utnij.pl');
            $transport->setPassword('PxpPUM6rQ7Vx');

            $transport->start();

            $output->writeln('Połączenie SMTP udane!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Błąd: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}