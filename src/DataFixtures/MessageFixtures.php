<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class MessageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $usernames = ['Zomboy7', 'Lococat', 'Vamp', 'Demonic', 'Reinghord', 'Masko', 'Solpadoin', 'exe', 'Oscarr', 'YarikBa4ok', 'Tomas', 'QQ'];
        $emails = ['example@gmail.com', 'example@googlemail.com', 'exampleanything@gmail.com', 'exa.mp.le@gmail.com', 'pehplusplus@gmail.com'];
        $homepages = ['https://root7.ru', 'https://github.com/zomboy7', 'https:/t.me/zomboy7', 'https://docs.google.com/document/d/16Wftc4lprKsUA6IdmGdwCUPdp01tq1UJm_64rspC-no/edit'];
        $texts = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse dolor ex, sagittis vitae sollicitudin elementum, malesuada vel nulla. Sed vestibulum velit nec faucibus molestie. Cras et odio vitae erat tristique eleifend a vel erat. Morbi et enim sed metus convallis varius. Vestibulum finibus nisi congue mollis rutrum. Nam sed felis justo. Morbi aliquet dolor at tellus consectetur imperdiet. Integer egestas placerat erat ut mattis.',
            'Fusce justo nisi, tempus sed cursus at, blandit at sem. Donec dictum sit amet ex semper dignissim. Phasellus iaculis enim iaculis ornare malesuada. Morbi a massa viverra felis laoreet semper vitae eu lorem. Nam lacus lectus, finibus et justo id, dictum aliquam velit. Vestibulum auctor arcu non erat eleifend elementum eget eu turpis. Pellentesque dapibus fermentum tortor eget dignissim. Nulla massa justo, laoreet eu arcu ut, pulvinar porta sem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aliquam pellentesque, urna vel blandit tincidunt, purus justo cursus nulla, ac mollis neque est sit amet tortor. Proin vitae consectetur quam. Nulla quam enim, euismod eget augue et, bibendum gravida dolor. Ut sed neque varius, lacinia leo non, fermentum ex.',
            'Sed mollis diam libero, sit amet imperdiet velit sodales accumsan. Proin fermentum dictum sapien, in egestas ligula commodo sed. Suspendisse potenti. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean sed neque interdum, pulvinar ante ut, vulputate neque. Proin lobortis mattis felis at malesuada. Etiam ullamcorper varius justo, nec tristique urna convallis non. Aenean eu semper massa, quis pretium erat. Nulla facilisi. Aliquam ullamcorper maximus egestas. Nulla hendrerit eu libero sed tristique. Suspendisse eu purus ipsum.',
            '<h1><span style="color:#3498db"><strong>Jeans</strong></span></h1><p><strong>Lorem ipsum dolor sit amet</strong>, consectetur adipiscing elit. Suspendisse arcu libero, posuere at nulla ut, viverra venenatis arcu. Curabitur pharetra, sapien sit amet convallis aliquam, mauris risus volutpat erat, vel vehicula nisi lectus ac urna. Proin volutpat non leo vitae vulputate. Nunc efficitur, turpis a egestas tincidunt, elit turpis pretium eros, at maximus mauris tellus.</p><hr /><p><span style="color:#3498db">Have fun! </span></p>',
            '<h1><span style="font-family:Courier New,Courier,monospace"><span style="color:#ffffff"><span style="background-color:#3498db">21st century architecture</span></span></span></h1><p><em>Aenean risus dolor, maximus placerat ante eu, venenatis condimentum diam. Duis accumsan accumsan odio, et aliquet lorem hendrerit eu. Vivamus non nisl vel leo congue mollis. Integer viverra, est at consectetur consequat, magna turpis finibus dui, a sagittis leo urna non sem. Integer at interdum justo, eget interdum ligula. Vivamus vehicula metus non eros aliquam blandit. Integer mauris massa, luctus quis sollicitudin sed, finibus a ex. Praesent pretium, nisl in faucibus eleifend, dolor dolor rhoncus erat, a consectetur eros diam vel orci.</em></p>',
            '<p><span style="color:#e74c3c"><span style="font-size:20px"><del><span style="background-color:#ecf0f1">You crazy!</span></del></span></span></p><hr /><p>Nunc a facilisis libero, ac semper ante. Aenean id tellus erat. Maecenas vitae sollicitudin lorem, vel maximus libero. Ut posuere pulvinar odio. Quisque scelerisque orci elit, vitae ultricies ex lacinia laoreet. Suspendisse mollis nulla nec nulla gravida, eget porttitor magna egestas. Nulla non enim non odio posuere vehicula. Etiam dictum orci posuere nisl euismod aliquet. Nunc id odio pellentesque, sodales justo at, luctus justo. Sed tristique tempus lobortis. Pellentesque ex ligula, sagittis ac magna rutrum, dapibus vestibulum mauris. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>',
            '<p><strong>Sed aliquet lacinia magna, et aliquam quam pulvinar non.</strong> Ut eleifend sem aliquam velit eleifend placerat. Integer arcu enim, facilisis in consectetur et, tincidunt nec massa. Nullam varius nunc nec lacus iaculis iaculis. Curabitur tortor justo, accumsan ut elit vitae, ultrices lobortis libero. Proin metus mauris, aliquet id sollicitudin vitae, porttitor ut sem. Fusce nec porttitor tortor. Aenean ac varius odio. <span style="color:#00ccff">Mauris vitae ex non</span> nisi volutpat pellentesque at quis tellus. Aliquam et tellus at lectus lacinia rutrum in a magna. Proin nibh felis, sodales non ex et, iaculis lacinia orci. Cras facilisis sagittis sem, eu dapibus lorem luctus eget. Aliquam erat volutpat. Duis in ante feugiat, mattis lorem nec, lobortis leo. Praesent diam arcu, venenatis vitae gravida ut, tincidunt scelerisque turpis.</p>',
            '<h2 style="font-style:italic"><span style="color:#4e5f70"><span style="font-family:Lucida Sans Unicode,Lucida Grande,sans-serif">Tortor justo, accumsan ut elit vitae, ultrices lobortis libero. </span></span></h2><br><hr /><p>Proin metus mauris, aliquet id sollicitudin vitae, porttitor ut sem. Fusce nec porttitor tortor. Aenean ac varius odio. Mauris vitae ex non nisi volutpat pellentesque at quis tellus. Aliquam et tellus at lectus lacinia rutrum in a magna. Proin nibh felis, sodales non ex et, iaculis lacinia orci. Cras facilisis sagittis sem, eu dapibus lorem luctus ...</p>'
        ];

        for ($i = 0; $i < 30; $i++ ) {
            $message = new Message();
            $message->setUsername($usernames[mt_rand(0, count($usernames)-1)]);
            $message->setEmail($emails[mt_rand(0, count($emails)-1)]);
            $message->setHomepage($homepages[mt_rand(0, count($homepages)-1)]);
            $message->setText($texts[mt_rand(0, count($texts)-1)]);
            $message->setCreatedAt(new \DateTime(rand(2018,2019).'/'.rand(1, 12).'/'.rand(1, 30)));
            $message->setUserIp(rand(1, 255).'.'.rand(1, 255).'.'.rand(1, 255).'.'.rand(1, 255));
            $message->setPicture('fixtures/'.rand(1, 25).'.jpg');
            $message->setIsEnabled(rand(0, 1));
            $manager->persist($message);
        }
        $manager->flush();
    }
}
