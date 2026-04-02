<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Avval UserSeeder ni ishga tushiring!');
            return;
        }

        $categories = ['Matematika', 'Fizika', 'Kimyo', 'Biologiya', 'Tarix', 'Ingliz tili', 'Informatika', 'E\'lon'];

        $newsData = [
            [
                'title'    => 'Abutolib platformasiga xush kelibsiz!',
                'excerpt'  => 'Online test tizimimiz rasmiy ishga tushdi. Barcha o\'quvchilar uchun bepul!',
                'body'     => "Abutolib — bu zamonaviy online test platformasi bo'lib, o'quvchilar va talabalar uchun mo'ljallangan.\n\nPlatformada quyidagi imkoniyatlar mavjud:\n- Turli fanlar bo'yicha testlar\n- Natijalarni kuzatish va tahlil qilish\n- Reyting tizimi\n- Shaxsiy kabinet\n\nBiz har kuni yangi testlar qo'shib boramiz. Sizni platformamizda ko'rishdan xursandmiz!",
                'category' => 'E\'lon',
                'image'    => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800',
            ],
            [
                'title'    => 'Matematika olimpiadasi natijalari e\'lon qilindi',
                'excerpt'  => '2025-yil respublika matematika olimpiadasi g\'oliblari aniqlandi.',
                'body'     => "Matematika olimpiadasi 2025-yil 15-mart kuni bo'lib o'tdi.\n\nBirinchi o'rin: Abdulloh Karimov — Toshkent shahri\nIkkinchi o'rin: Malika Yusupova — Samarqand viloyati\nUchinchi o'rin: Bobur Toshmatov — Farg'ona viloyati\n\nBarcha g'oliblarni tabriklaymiz! Ular respublika darajasidagi tanlovda qatnashish huquqini qo'lga kiritdilar.\n\nKelgusi olimpiada 2025-yil noyabr oyida o'tkaziladi.",
                'category' => 'Matematika',
                'image'    => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=800',
            ],
            [
                'title'    => 'Fizika fanidan yangi test to\'plami qo\'shildi',
                'excerpt'  => '200 ta yangi fizika testi platformaga yuklandi. Mexanika va termodinamika bo\'limlari.',
                'body'     => "Fizika fani bo'yicha yangi test to'plami tayyorlandi.\n\nTo'plam quyidagi bo'limlarni qamrab oladi:\n\n1. Mexanika\n   - Kinematika\n   - Dinamika\n   - Statika\n\n2. Termodinamika\n   - Issiqlik almashinuvi\n   - Gaz qonunlari\n\n3. Elektr va magnetizm\n\nTestlar maktab dasturiga to'liq mos keladi va imtihonlarga tayyorgarlik uchun ideal vosita hisoblanadi.",
                'category' => 'Fizika',
                'image'    => 'https://images.unsplash.com/photo-1636466497217-26a8cbeaf0aa?w=800',
            ],
            [
                'title'    => 'Kimyo fanidan interaktiv darslar boshlandi',
                'excerpt'  => 'Kimyo bo\'yicha video darslar va testlar bir platformada. Bepul kirish imkoniyati mavjud.',
                'body'     => "Kimyo fanini o'rganish endi yanada qiziqarli!\n\nPlatformamizda kimyo bo'yicha quyidagilar mavjud:\n\n- Organik kimyo testlari\n- Anorganik kimyo testlari\n- Reaksiya tenglamalarini muvozanatlash mashqlari\n- Davriy jadval bo'yicha savollar\n\nHar bir mavzu bo'yicha 50 tadan ortiq test savoli kiritilgan. Savollar murakkablik darajasiga ko'ra ajratilgan: boshlang'ich, o'rta va yuqori daraja.",
                'category' => 'Kimyo',
                'image'    => 'https://images.unsplash.com/photo-1532094349884-543559242a4e?w=800',
            ],
            [
                'title'    => 'IELTS tayyorgarlik kursi boshlandi',
                'excerpt'  => 'Ingliz tili IELTS imtihoniga tayyorgarlik uchun maxsus test to\'plami.',
                'body'     => "IELTS imtihoniga tayyorgarlik ko'rayotganlar uchun ajoyib yangilik!\n\nPlatformamizda IELTS bo'limlari bo'yicha testlar:\n\nReading:\n- Academic Reading\n- General Reading\n\nListening:\n- Section 1-4 mashqlari\n\nWriting:\n- Task 1 va Task 2 namunalari\n\nGrammar:\n- Tenses\n- Conditionals\n- Passive Voice\n\nTestlar haqiqiy IELTS formati asosida tuzilgan.",
                'category' => 'Ingliz tili',
                'image'    => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=800',
            ],
            [
                'title'    => 'Biologiya: Hujayra tuzilishi bo\'yicha yangi testlar',
                'excerpt'  => 'Tirik organizmlar hujayrasining tuzilishi va vazifalari bo\'yicha 150 ta yangi test.',
                'body'     => "Biologiya fanida hujayra mavzusi eng muhim bo'limlardan biri hisoblanadi.\n\nYangi test to'plami quyidagilarni o'z ichiga oladi:\n\n1. Hujayraning kimyoviy tarkibi\n2. Hujayra organoidlari\n3. Prokaryot va eukaryot hujayralar\n4. Hujayra bo'linishi\n   - Mitoz\n   - Meyoz\n5. Fotosintez va nafas olish\n\nHar bir bo'lim bo'yicha izohli javoblar ham berilgan.",
                'category' => 'Biologiya',
                'image'    => 'https://images.unsplash.com/photo-1530026186672-2cd00ffc50fe?w=800',
            ],
            [
                'title'    => 'Tarix: O\'zbekiston mustaqilligi mavzusi testlari',
                'excerpt'  => 'O\'zbekiston tarixi, mustaqillik davri va uning ahamiyati bo\'yicha test savollari.',
                'body'     => "O'zbekiston tarixi bo'yicha yangi test to'plami tayyorlandi.\n\nMavzular:\n\n1. Mustaqillikning e'lon qilinishi (1991)\n2. Birinchi prezident I.Karimov davri\n3. Konstitutsiyaning qabul qilinishi\n4. Iqtisodiy islohotlar\n5. Xalqaro munosabatlar\n6. Shavkat Mirziyoyev davridagi o'zgarishlar\n\nTestlar maktab dasturi va DTM talablari asosida tuzilgan.",
                'category' => 'Tarix',
                'image'    => 'https://images.unsplash.com/photo-1461360228754-6e81c478b882?w=800',
            ],
            [
                'title'    => 'Informatika: Python dasturlash tili bo\'yicha testlar',
                'excerpt'  => 'Python asoslari, o\'zgaruvchilar, funksiyalar va OOP bo\'yicha 100 ta savol.',
                'body'     => "Informatika fanida dasturlash bo'limi uchun Python testlari qo'shildi.\n\nMavzular:\n\n1. Python asoslari\n   - O'zgaruvchilar va ma'lumot turlari\n   - Shartli operatorlar\n   - Tsikllar\n\n2. Funksiyalar\n   - def kalit so'zi\n   - Parametrlar va qaytarish qiymati\n\n3. Ro'yxatlar va lug'atlar\n\n4. OOP asoslari\n   - Klass va obyekt\n   - Meros olish\n\nHar bir savol kod namunasi bilan birga keladi.",
                'category' => 'Informatika',
                'image'    => 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=800',
            ],
            [
                'title'    => 'Yangi o\'quv yili: Jadval va rejalar',
                'excerpt'  => '2025-2026 o\'quv yili uchun test jadval va imtihon sanalari e\'lon qilindi.',
                'body'     => "2025-2026 o'quv yili uchun muhim sanalar:\n\nSentabr:\n- 1-sentabr: O'quv yili boshlanishi\n- 15-sentabr: Kirish testlari\n\nOktabr:\n- 1-10 oktabr: I chorak testlari\n\nNoyabr:\n- 20-30 noyabr: Olimpiadalar boshlang'ich bosqichi\n\nYanvar:\n- 15-25 yanvar: Yarim yillik testlar\n\nMart:\n- Respublika olimpiadalari\n\nMay-Iyun:\n- DTM imtihonlari tayyorgarlik testlari\n- Yakuniy imtihonlar",
                'category' => 'E\'lon',
                'image'    => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800',
            ],
            [
                'title'    => 'DTM 2025: Eng ko\'p uchraydigan savollar tahlili',
                'excerpt'  => 'O\'tgan yilgi DTM imtihonlarida eng ko\'p uchraydigan mavzular va savollar tahlili.',
                'body'     => "DTM 2025 imtihoniga tayyorlanayotganlar uchun foydali ma'lumot.\n\nMATEMATIKA:\nEng ko'p uchraydigan mavzular:\n- Trigonometriya (25%)\n- Integral va differensial (20%)\n- Logarifm (15%)\n- Geometriya (20%)\n\nFIZIKA:\n- Mexanika (30%)\n- Elektromagnetizm (25%)\n- Optika (15%)\n\nKIMYO:\n- Organik kimyo (40%)\n- Eritmalar (25%)\n\nHar bir mavzu bo'yicha platformamizda maxsus test to'plamlari mavjud.",
                'category' => 'E\'lon',
                'image'    => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800',
            ],
            [
                'title'    => 'Algebra: Tengsizliklar va ularni yechish usullari',
                'excerpt'  => 'Murakkab tengsizliklarni yechishning eng samarali usullari va misollar.',
                'body'     => "Algebra bo'limida tengsizliklar mavzusi ko'p o'quvchilarga qiyin tuyuladi.\n\nAsosiy usullar:\n\n1. Grafik usul\n   - Funktsiya grafigini chizish\n   - Kesishish nuqtalarini topish\n\n2. Interval usuli\n   - Ko'paytmaga ajratish\n   - Ishoralar jadvalini tuzish\n\n3. Modul bilan tengsizliklar\n   - |x| < a => -a < x < a\n   - |x| > a => x < -a yoki x > a\n\nMisol va mashqlar platformamizda mavjud.",
                'category' => 'Matematika',
                'image'    => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=800',
            ],
            [
                'title'    => 'Ingliz tili: Grammar testlari yangilandi',
                'excerpt'  => 'Past Simple, Present Perfect va boshqa zamonlar bo\'yicha 300 ta yangi test.',
                'body'     => "Ingliz tili grammatikasi bo'yicha yangi testlar qo'shildi.\n\nMavzular:\n\nTenses:\n- Present Simple / Continuous\n- Past Simple / Continuous / Perfect\n- Future Simple / Continuous\n\nOther Topics:\n- Modal Verbs\n- Passive Voice\n- Reported Speech\n- Conditionals (0, 1, 2, 3)\n- Articles\n- Prepositions\n\nHar bir mavzu bo'yicha 20-30 ta savol mavjud bo'lib, qiyinlik darajasiga ko'ra ajratilgan.",
                'category' => 'Ingliz tili',
                'image'    => 'https://images.unsplash.com/photo-1546521343-4eb2c01aa44b?w=800',
            ],
        ];

        foreach ($newsData as $index => $data) {
            News::create([
                'title'        => $data['title'],
                'slug'         => Str::slug($data['title']) . '-' . Str::random(4),
                'excerpt'      => $data['excerpt'],
                'body'         => $data['body'],
                'image'        => $data['image'],
                'category'     => $data['category'],
                'status'       => 'published',
                'views'        => rand(50, 2000),
                'user_id'      => $users->random()->id,
                'published_at' => now()->subDays(rand(1, 60)),
            ]);
        }

        $this->command->info('✅ ' . count($newsData) . ' ta yangilik yaratildi!');
    }
}
