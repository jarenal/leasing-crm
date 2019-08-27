<?php

namespace App\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FakerTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:fakertest')
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
        $names = array(
        'Aaron', 'Adam', 'Adrian', 'Aiden', 'Alan', 'Alex', 'Alexander', 'Alfie', 'Andrew', 'Andy', 'Anthony', 'Archie', 'Arthur',
        'Barry', 'Ben', 'Benjamin', 'Bradley', 'Brandon', 'Bruce',
        'Callum', 'Cameron', 'Charles', 'Charlie', 'Chris', 'Christian', 'Christopher', 'Colin', 'Connor', 'Craig',
        'Dale', 'Damien', 'Dan', 'Daniel', 'Darren', 'Dave', 'David', 'Dean', 'Dennis', 'Dominic', 'Duncan', 'Dylan',
        'Edward', 'Elliot', 'Elliott', 'Ethan',
        'Finley', 'Frank', 'Fred', 'Freddie',
        'Gary', 'Gavin', 'George', 'Gordon', 'Graham', 'Grant', 'Greg',
        'Harley', 'Harrison', 'Harry', 'Harvey', 'Henry',
        'Ian', 'Isaac',
        'Jack', 'Jackson', 'Jacob', 'Jake', 'James', 'Jamie', 'Jason', 'Jayden', 'Jeremy', 'Jim', 'Joe', 'Joel', 'John', 'Jonathan', 'Jordan', 'Joseph', 'Joshua',
        'Karl', 'Keith', 'Ken', 'Kevin', 'Kieran', 'Kyle',
        'Lee', 'Leo', 'Lewis', 'Liam', 'Logan', 'Louis', 'Lucas', 'Luke',
        'Mark', 'Martin', 'Mason', 'Matthew', 'Max', 'Michael', 'Mike', 'Mohammed', 'Muhammad',
        'Nathan', 'Neil', 'Nick', 'Noah',
        'Oliver', 'Oscar', 'Owen',
        'Patrick', 'Paul', 'Pete', 'Peter', 'Philip',
        'Quentin',
        'Ray', 'Reece', 'Riley', 'Rob', 'Ross', 'Ryan',
        'Samuel', 'Scott', 'Sean', 'Sebastian', 'Stefan', 'Stephen', 'Steve',
        'Theo', 'Thomas', 'Tim', 'Toby', 'Tom', 'Tony', 'Tyler',
        'Wayne', 'Will', 'William',
        'Zachary', 'Zach'
    );

        $allKeys = array_keys($names);
        $numKeys = count($allKeys);

        $highKey = $numKeys - 1;
        /*
        $faker = \Faker\Factory::create('en_GB');

        $original = "Graham";

        for($x=0;$x<100000;$x++)
        {
            $faker->seed($x);
            $last = $faker->firstName;

            if($original==$last)
            {
                echo "\nEncontrado en....................".$x." - ".$last;
                break;
            }
            echo "\n".$x." - ".$last;
        }*/

        $faker = \Faker\Factory::create('en_GB');
        $faker->seed(101);


        echo "\n".$faker->company;
        echo "\n".$faker->phoneNumber;
        echo "\n".$faker->email;
        echo "\n".$faker->url;
        echo "\n".$faker->streetAddress;
        echo "\n".$faker->postcode;
        echo "\n".$faker->city;

        $faker->seed(101);
        echo "\n".$faker->firstName;
        echo "\n".$faker->lastName;
        echo "\n".$faker->email;
        echo "\n---------------------------------------\n";

        $faker->seed(102);


        echo "\n".$faker->company;
        echo "\n".$faker->phoneNumber;
        echo "\n".$faker->email;
        echo "\n".$faker->url;
        echo "\n".$faker->streetAddress;
        echo "\n".$faker->postcode;
        echo "\n".$faker->city;

        $faker->seed(102);
        echo "\n".$faker->firstName;
        echo "\n".$faker->lastName;
        echo "\n".$faker->email;
        echo "\n---------------------------------------\n";        

        $faker->seed(101);


        echo "\n".$faker->company;
        echo "\n".$faker->phoneNumber;
        echo "\n".$faker->email;
        echo "\n".$faker->url;
        echo "\n".$faker->streetAddress;
        echo "\n".$faker->postcode;
        echo "\n".$faker->city;

        $faker->seed(101);
        echo "\n".$faker->firstName;
        echo "\n".$faker->lastName;
        echo "\n".$faker->email;
        echo "\n---------------------------------------\n";        

/*
            mt_srand(100);
            $key = mt_rand(0, $highKey);

            echo "\nKEY: ".$key;
            echo "\nNOMBRE: ".$names[$allKeys[$key]];

            echo "\n";
*/

    }
}
