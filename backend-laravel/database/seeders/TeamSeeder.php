<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name'      => 'Abdulloh Karimov',
                'role'      => 'Founder & CEO',
                'avatar'    => 'https://api.dicebear.com/7.x/initials/svg?seed=AK&backgroundColor=3b82f6&fontFamily=Arial&fontSize=40',
                'email'     => 'abdulloh@abutolib.uz',
                'telegram'  => 'abdulloh_dev',
                'instagram' => 'abdulloh.karimov',
                'bio'       => 'Platforma asoschiси va bosh dasturchi. 5 yillik tajribaga ega full-stack developer.',
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'name'      => 'Malika Yusupova',
                'role'      => 'Content Manager',
                'avatar'    => 'https://api.dicebear.com/7.x/initials/svg?seed=MY&backgroundColor=8b5cf6&fontFamily=Arial&fontSize=40',
                'email'     => 'malika@abutolib.uz',
                'telegram'  => 'malika_content',
                'instagram' => 'malika.yusupova',
                'bio'       => 'Kontent yaratish va test savollarini nazorat qilish. Pedagogika fanlari magistri.',
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'name'      => 'Bobur Toshmatov',
                'role'      => 'Backend Developer',
                'avatar'    => 'https://api.dicebear.com/7.x/initials/svg?seed=BT&backgroundColor=10b981&fontFamily=Arial&fontSize=40',
                'email'     => 'bobur@abutolib.uz',
                'telegram'  => 'bobur_backend',
                'instagram' => null,
                'bio'       => 'Laravel va API dasturlash bo\'yicha mutaxassis. 3 yillik tajriba.',
                'order'     => 3,
                'is_active' => true,
            ],
            [
                'name'      => 'Nilufar Hasanova',
                'role'      => 'UI/UX Designer',
                'avatar'    => 'https://api.dicebear.com/7.x/initials/svg?seed=NH&backgroundColor=f59e0b&fontFamily=Arial&fontSize=40',
                'email'     => 'nilufar@abutolib.uz',
                'telegram'  => 'nilufar_design',
                'instagram' => 'nilufar.design',
                'bio'       => 'Foydalanuvchi interfeysi va tajribasi dizayni. Figma va Adobe XD mutaxassisi.',
                'order'     => 4,
                'is_active' => true,
            ],
            [
                'name'      => 'Sardor Ergashev',
                'role'      => 'Frontend Developer',
                'avatar'    => 'https://api.dicebear.com/7.x/initials/svg?seed=SE&backgroundColor=ef4444&fontFamily=Arial&fontSize=40',
                'email'     => 'sardor@abutolib.uz',
                'telegram'  => 'sardor_front',
                'instagram' => null,
                'bio'       => 'Vue.js va Nuxt.js bo\'yicha dasturchi. Responsiv dizayn va animatsiyalar.',
                'order'     => 5,
                'is_active' => true,
            ],
            [
                'name'      => 'Zulfiya Rahimova',
                'role'      => 'Math Teacher',
                'avatar'    => 'https://api.dicebear.com/7.x/initials/svg?seed=ZR&backgroundColor=06b6d4&fontFamily=Arial&fontSize=40',
                'email'     => 'zulfiya@abutolib.uz',
                'telegram'  => 'zulfiya_math',
                'instagram' => 'zulfiya.teacher',
                'bio'       => 'Matematika fani bo\'yicha test muallifi. 10 yillik o\'qituvchilik tajribasi.',
                'order'     => 6,
                'is_active' => true,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::create($member);
        }

        $this->command->info('✅ ' . count($members) . ' ta jamoa a\'zosi yaratildi!');
    }
}
