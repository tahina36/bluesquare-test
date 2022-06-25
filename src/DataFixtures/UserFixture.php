<?php

namespace App\DataFixtures;

use App\Entity\EventType;
use App\Entity\Group;
use App\Entity\GroupPermission;
use App\Entity\Project;
use App\Entity\Trip;
use App\Entity\TripStep;
use App\Entity\User;
use App\Entity\UserGroup;
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

        $eventTypes = [
            [
                "name" => "ticket_creation",
            ],
            [
                "name" => "new_comment",
            ],
            [
                "name" => "file_upload",
            ],
            [
                "name" => "status_updated",
            ],
            [
                "name" => "priority_updated",
            ],
        ] ;



        foreach ($eventTypes as $eventTypeDatas) {
            $eventType = new EventType();
            $eventType->setName($eventTypeDatas["name"]);
            $manager->persist($eventType);
        }


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
            foreach ($userDatas["groups"] as $group) {
                $userGroup = new UserGroup();
                $group = $manager->getRepository(Group::class)->findOneBy(["name" => $group]);
                $userGroup->setGroup($group);
                $userGroup->setUser($user);
                $manager->persist($userGroup);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
