<?php

namespace App\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\BackendBundle\Entity\LandlordStatus;

class Data2JavascriptCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:data2javascript')
            ->setDescription('Greet someone')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);*/

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $query = $em->createQuery("SELECT s FROM AppBackendBundle:LandlordStatus s");
        $status = $query->getResult();   
        
        foreach ($status as $status) 
        {
            $output->writeln($status->getName());
        }     
    }
}
