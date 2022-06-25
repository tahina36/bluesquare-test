<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\GroupPermission;
use App\Entity\Project;
use App\Entity\Trip;
use App\Entity\TripStep;
use App\Entity\User;
use App\Service\Helper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groups = [
            [
                "name" => "groupe test 1",
            ],
            [
                "name" => "groupe test 2",
            ],
        ];

        $projects = [
            [
                "name" => "projet test 1",
                "permissions" => [
                    "groupe test 1"
                ]
            ],
            [
                "name" => "projet test 2",
                "permissions" => [
                    "groupe test 2"
                ]
            ],
        ];

        $users = [
            [
                "firstname" => "tahina",
                "email" => "tahina.ramani01@gmail.com",
                "password" => "ramani",
                "groups" => ["groupe test 1"],
            ],
            [
                "firstname" => "michel",
                "email" => "michel@gmail.com",
                "password" => "toto",
                "groups" => ["groupe test 2"],
            ],
        ];



        foreach ($groups as $groupsDatas) {
            $group = new Group();
            $group->setName($groupsDatas["name"]);
            $manager->persist($group);
        }
        $manager->flush();

        foreach ($projects as $projectDatas) {
            $project = new Project();
            $project->setName($projectDatas["name"]);
            $manager->persist($project);
            foreach ($projectDatas["permissions"] as $permission) {
                $groupPermission = new GroupPermission();
                $group = $manager->getRepository(Group::class)->findOneBy(["name" => $permission]);
                $groupPermission->setGroup($group);
                $groupPermission->setProject($project);
                $manager->persist($groupPermission);
            }
        }

        foreach ($users as $userDatas) {
            $user = new User();
            $user->setFirstname($userDatas["firstname"]);
            $user->setEmail($userDatas["email"]);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(password_hash($userDatas["password"], PASSWORD_DEFAULT));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
