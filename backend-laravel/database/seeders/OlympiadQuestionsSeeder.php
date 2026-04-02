<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OlympiadQuestion;
use App\Models\OlympiadAnswer;
use Illuminate\Support\Facades\DB;

class OlympiadQuestionsSeeder extends Seeder
{
    private int $olympiadId = 6;

    public function run(): void
    {
        // Avvalgi savollarni o'chirish (ixtiyoriy)
        // DB::table('olympiad_answers')->whereIn('olympiad_question_id',
        //     OlympiadQuestion::where('olympiad_id', $this->olympiadId)->pluck('id')
        // )->delete();
        // OlympiadQuestion::where('olympiad_id', $this->olympiadId)->delete();

        DB::transaction(function () {
            foreach ($this->questions() as $index => $data) {
                $question = OlympiadQuestion::create([
                    'olympiad_id' => $this->olympiadId,
                    'question'    => $data['question'],
                    'type'        => 'single',
                    'difficulty'  => 'medium',
                    'order'       => $index,
                ]);

                foreach ($data['answers'] as $answer) {
                    OlympiadAnswer::create([
                        'olympiad_question_id' => $question->id,
                        'answer'               => $answer['answer'],
                        'is_correct'           => $answer['is_correct'],
                    ]);
                }
            }
        });

        $this->command->info('200 ta savol muvaffaqiyatli qo\'shildi (olympiad_id = ' . $this->olympiadId . ')');
    }

    private function questions(): array
    {
        return [
            [
                'question' => 'PHP da API ga HTTP so\'rov yuborish uchun qaysi funksiya ishlatiladi?',
                'answers'  => [
                    ['answer' => 'file_get()', 'is_correct' => false],
                    ['answer' => 'curl_init()', 'is_correct' => true],
                    ['answer' => 'http_request()', 'is_correct' => false],
                    ['answer' => 'api_send()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'CURLOPT_RETURNTRANSFER parametri nima qiladi?',
                'answers'  => [
                    ['answer' => 'So\'rovni yuboradi', 'is_correct' => false],
                    ['answer' => 'Ulanishni yopadi', 'is_correct' => false],
                    ['answer' => 'Javobni qaytaradi (echo emas)', 'is_correct' => true],
                    ['answer' => 'Xatoni ko\'rsatadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'API kalitni qayerda saqlash xavfsiz?',
                'answers'  => [
                    ['answer' => 'index.php faylida', 'is_correct' => false],
                    ['answer' => '.env faylida', 'is_correct' => true],
                    ['answer' => 'HTML faylida', 'is_correct' => false],
                    ['answer' => 'CSS faylida', 'is_correct' => false],
                ],
            ],
            [
                'question' => '.gitignore fayliga .env yozish nima uchun kerak?',
                'answers'  => [
                    ['answer' => 'Faylni o\'chirish uchun', 'is_correct' => false],
                    ['answer' => 'Kodni tezlashtirish uchun', 'is_correct' => false],
                    ['answer' => 'API kalitni GitHub ga yuklamaslik uchun', 'is_correct' => true],
                    ['answer' => 'CSS uslubini saqlash uchun', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'json_encode() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'JSON faylni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'PHP massivini JSON ga aylantiradi', 'is_correct' => true],
                    ['answer' => 'JSON ni tekshiradi', 'is_correct' => false],
                    ['answer' => 'Faylni o\'qiydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'json_decode($json, true) da true parametri nima qiladi?',
                'answers'  => [
                    ['answer' => 'Xatolarni ko\'rsatadi', 'is_correct' => false],
                    ['answer' => 'Obyekt o\'rniga massiv qaytaradi', 'is_correct' => true],
                    ['answer' => 'Faylga yozadi', 'is_correct' => false],
                    ['answer' => 'JSON ni formatlashtiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq API da qaysi model eng kuchli va ko\'p maqsadli?',
                'answers'  => [
                    ['answer' => 'gpt-3.5-turbo', 'is_correct' => false],
                    ['answer' => 'llama3-8b-8192', 'is_correct' => false],
                    ['answer' => 'llama-3.3-70b-versatile', 'is_correct' => true],
                    ['answer' => 'mixtral-7b', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Bearer token autentifikatsiyada token qaysi headerda yuboriladi?',
                'answers'  => [
                    ['answer' => 'Content-Type', 'is_correct' => false],
                    ['answer' => 'Accept', 'is_correct' => false],
                    ['answer' => 'Authorization', 'is_correct' => true],
                    ['answer' => 'User-Agent', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'curl_close($ch) nima uchun ishlatiladi?',
                'answers'  => [
                    ['answer' => 'Javobni qaytarish uchun', 'is_correct' => false],
                    ['answer' => 'cURL ulanishini yopish uchun', 'is_correct' => true],
                    ['answer' => 'Xatoni tekshirish uchun', 'is_correct' => false],
                    ['answer' => 'So\'rov yuborish uchun', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq API ning asosiy URL manzili qaysi?',
                'answers'  => [
                    ['answer' => 'https://api.openai.com/v1/', 'is_correct' => false],
                    ['answer' => 'https://api.groq.com/v1/', 'is_correct' => false],
                    ['answer' => 'https://api.groq.com/openai/v1/chat/completions', 'is_correct' => true],
                    ['answer' => 'https://groq.ai/api/chat', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da cURL so\'rovida Content-Type headeri nima ko\'rsatadi?',
                'answers'  => [
                    ['answer' => 'Serverning joylashuvi', 'is_correct' => false],
                    ['answer' => 'Yuboriladigan ma\'lumot formati', 'is_correct' => true],
                    ['answer' => 'API versiyasi', 'is_correct' => false],
                    ['answer' => 'Javob tili', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'API dan qaytgan JSON javobni o\'qish uchun qaysi funksiya ishlatiladi?',
                'answers'  => [
                    ['answer' => 'json_encode()', 'is_correct' => false],
                    ['answer' => 'json_decode()', 'is_correct' => true],
                    ['answer' => 'json_read()', 'is_correct' => false],
                    ['answer' => 'json_parse()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Quyidagi kodda xato nima? $result = curl_exec($ch); echo $result[\'choices\'];',
                'answers'  => [
                    ['answer' => 'curl_exec() noto\'g\'ri', 'is_correct' => false],
                    ['answer' => '$ch aniqlanmagan', 'is_correct' => false],
                    ['answer' => 'json_decode() chaqirilmagan', 'is_correct' => true],
                    ['answer' => 'Xato yo\'q', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da getenv() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'PHP versiyasini qaytaradi', 'is_correct' => false],
                    ['answer' => 'Environment variable qiymatini oladi', 'is_correct' => true],
                    ['answer' => 'Faylni o\'qiydi', 'is_correct' => false],
                    ['answer' => 'Serverga ulanadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq API ga so\'rov yuborishda messages massivi qanday tuziladi?',
                'answers'  => [
                    ['answer' => '[\'savol\' => \'...\']', 'is_correct' => false],
                    ['answer' => '[[\'role\' => \'user\', \'content\' => \'...\']]', 'is_correct' => true],
                    ['answer' => '[\'content\' => \'...\']', 'is_correct' => false],
                    ['answer' => '[\'text\' => \'...\']', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'System prompt nima?',
                'answers'  => [
                    ['answer' => 'Foydalanuvchi savoli', 'is_correct' => false],
                    ['answer' => 'API kaliti', 'is_correct' => false],
                    ['answer' => 'AI ning rolini va xulq-atvorini belgilovchi ko\'rsatma', 'is_correct' => true],
                    ['answer' => 'Javob formati', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'curl_getinfo($ch, CURLINFO_HTTP_CODE) nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Javob matnini', 'is_correct' => false],
                    ['answer' => 'cURL versiyasini', 'is_correct' => false],
                    ['answer' => 'HTTP status kodi (200, 404 va h.k.)', 'is_correct' => true],
                    ['answer' => 'Ulanish vaqtini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da trim() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Matnni qisqartiradi', 'is_correct' => false],
                    ['answer' => 'Matn boshidagi va oxiridagi bo\'shliqlarni olib tashlaydi', 'is_correct' => true],
                    ['answer' => 'Matnni katta harfga aylantiradi', 'is_correct' => false],
                    ['answer' => 'Matnni bo\'ladi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq API "insufficient_quota" xatosi nima degani?',
                'answers'  => [
                    ['answer' => 'API kalit noto\'g\'ri', 'is_correct' => false],
                    ['answer' => 'Model mavjud emas', 'is_correct' => false],
                    ['answer' => 'Kredit tugagan — to\'lash kerak', 'is_correct' => true],
                    ['answer' => 'Serverda xato', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da ?? operatori nima qiladi?',
                'answers'  => [
                    ['answer' => 'Ikkita qiymatni qo\'shadi', 'is_correct' => false],
                    ['answer' => 'Tenglikni tekshiradi', 'is_correct' => false],
                    ['answer' => 'O\'zgaruvchi null bo\'lsa standart qiymat qaytaradi', 'is_correct' => true],
                    ['answer' => 'Mantiqiy VA operatori', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq API da "model decommissioned" xatosi nima?',
                'answers'  => [
                    ['answer' => 'API kalit eskirgan', 'is_correct' => false],
                    ['answer' => 'Model o\'chirilgan, yangi model ishlatish kerak', 'is_correct' => true],
                    ['answer' => 'Internet yo\'q', 'is_correct' => false],
                    ['answer' => 'Kredit tugagan', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da CURLOPT_TIMEOUT parametri nima qiladi?',
                'answers'  => [
                    ['answer' => 'Serverga ulanish tezligini oshiradi', 'is_correct' => false],
                    ['answer' => 'Kutish vaqtini soniyada cheklaydi', 'is_correct' => true],
                    ['answer' => 'Javob hajmini cheklaydi', 'is_correct' => false],
                    ['answer' => 'Xatolarni yashiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da echo va print farqi nima?',
                'answers'  => [
                    ['answer' => 'Farq yo\'q', 'is_correct' => false],
                    ['answer' => 'echo tezroq, print sekinroq', 'is_correct' => false],
                    ['answer' => 'echo bir nechta qiymat chiqaradi, print faqat bitta va qiymat qaytaradi', 'is_correct' => true],
                    ['answer' => 'print faqat raqamlar uchun', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Quyidagi kod nima chiqaradi? echo json_encode([\'a\' => 1, \'b\' => 2]);',
                'answers'  => [
                    ['answer' => 'Array(a=>1, b=>2)', 'is_correct' => false],
                    ['answer' => 'a=1&b=2', 'is_correct' => false],
                    ['answer' => '{"a":1,"b":2}', 'is_correct' => true],
                    ['answer' => '[a,1,b,2]', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP 8.x dagi match() operatori switch() dan qanday farq qiladi?',
                'answers'  => [
                    ['answer' => 'Farq yo\'q', 'is_correct' => false],
                    ['answer' => 'match() faqat raqamlar uchun', 'is_correct' => false],
                    ['answer' => 'match() qiymat qaytaradi va strict taqqoslaydi, break shart emas', 'is_correct' => true],
                    ['answer' => 'switch() tezroq ishlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'API ga so\'rov yuborishda CURLOPT_POST true qilinmasa nima bo\'ladi?',
                'answers'  => [
                    ['answer' => 'Xato chiqadi', 'is_correct' => false],
                    ['answer' => 'GET so\'rovi yuboriladi', 'is_correct' => true],
                    ['answer' => 'POST yuboriladi', 'is_correct' => false],
                    ['answer' => 'Ulanish uziladi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da sprintf() funksiyasi nima uchun?',
                'answers'  => [
                    ['answer' => 'Matnni chiqarish', 'is_correct' => false],
                    ['answer' => 'Formatlangan matn yaratish', 'is_correct' => true],
                    ['answer' => 'Raqamlarni qo\'shish', 'is_correct' => false],
                    ['answer' => 'Faylga yozish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'cURL da SSL sertifikatni tekshirishni o\'chirish uchun qaysi parametr?',
                'answers'  => [
                    ['answer' => 'CURLOPT_SSL_VERIFY', 'is_correct' => false],
                    ['answer' => 'CURLOPT_NOSSL', 'is_correct' => false],
                    ['answer' => 'CURLOPT_SSL_VERIFYPEER => false', 'is_correct' => true],
                    ['answer' => 'CURLOPT_HTTPS => false', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da implode() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni bo\'ladi', 'is_correct' => false],
                    ['answer' => 'Massiv elementlarini birlashtirib matn qaytaradi', 'is_correct' => true],
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                    ['answer' => 'Massivni o\'chiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq API da "role" maydoni qanday qiymatlar qabul qiladi?',
                'answers'  => [
                    ['answer' => 'admin, user, bot', 'is_correct' => false],
                    ['answer' => 'system, user, assistant', 'is_correct' => true],
                    ['answer' => 'human, ai, system', 'is_correct' => false],
                    ['answer' => 'sender, receiver', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_map() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                    ['answer' => 'Massiv hajmini qaytaradi', 'is_correct' => false],
                    ['answer' => 'Har bir elementga funksiya qo\'llaydi', 'is_correct' => true],
                    ['answer' => 'Massivni filtrlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da str_repeat() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Matnni qisqartiradi', 'is_correct' => false],
                    ['answer' => 'Matnni n marta takrorlaydi', 'is_correct' => true],
                    ['answer' => 'Matnda qidiradi', 'is_correct' => false],
                    ['answer' => 'Matnni almashtiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da PHP_EOL konstantasi nima?',
                'answers'  => [
                    ['answer' => 'PHP versiyasi', 'is_correct' => false],
                    ['answer' => 'Operatsion tizimga mos satr uzish belgisi', 'is_correct' => true],
                    ['answer' => 'Fayl oxiri belgisi', 'is_correct' => false],
                    ['answer' => 'Bo\'sh satr', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'OOP ning to\'liq nomi nima?',
                'answers'  => [
                    ['answer' => 'Open Oriented Programming', 'is_correct' => false],
                    ['answer' => 'Object Oriented Programming', 'is_correct' => true],
                    ['answer' => 'Oriented Object PHP', 'is_correct' => false],
                    ['answer' => 'Output Organized Program', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da klass yaratish uchun qaysi kalit so\'z ishlatiladi?',
                'answers'  => [
                    ['answer' => 'object', 'is_correct' => false],
                    ['answer' => 'type', 'is_correct' => false],
                    ['answer' => 'class', 'is_correct' => true],
                    ['answer' => 'struct', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Obyekt yaratish uchun qaysi kalit so\'z ishlatiladi?',
                'answers'  => [
                    ['answer' => 'create', 'is_correct' => false],
                    ['answer' => 'instance', 'is_correct' => false],
                    ['answer' => 'object', 'is_correct' => false],
                    ['answer' => 'new', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'Konstruktor metodi qachon chaqiriladi?',
                'answers'  => [
                    ['answer' => 'Klass e\'lon qilinganda', 'is_correct' => false],
                    ['answer' => 'Obyekt yaratilganda (new)', 'is_correct' => true],
                    ['answer' => 'Metod chaqirilganda', 'is_correct' => false],
                    ['answer' => 'Dastur tugaganda', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da konstruktor metodi qanday nomlanadi?',
                'answers'  => [
                    ['answer' => 'constructor()', 'is_correct' => false],
                    ['answer' => 'init()', 'is_correct' => false],
                    ['answer' => '__construct()', 'is_correct' => true],
                    ['answer' => 'create()', 'is_correct' => false],
                ],
            ],
            [
                'question' => '$this kalit so\'zi nimani anglatadi?',
                'answers'  => [
                    ['answer' => 'Ota-klassni', 'is_correct' => false],
                    ['answer' => 'Global o\'zgaruvchini', 'is_correct' => false],
                    ['answer' => 'Statik metodlarni', 'is_correct' => false],
                    ['answer' => 'Joriy obyektning o\'zini', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'private xususiyatga qayerdan kirish mumkin?',
                'answers'  => [
                    ['answer' => 'Hamma joydan', 'is_correct' => false],
                    ['answer' => 'Faqat vorislardan', 'is_correct' => false],
                    ['answer' => 'Faqat shu klass ichidan', 'is_correct' => true],
                    ['answer' => 'Faqat konstruktordan', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'public xususiyat qayerdan ko\'rinadi?',
                'answers'  => [
                    ['answer' => 'Faqat klass ichidan', 'is_correct' => false],
                    ['answer' => 'Faqat vorislardan', 'is_correct' => false],
                    ['answer' => 'Hamma joydan', 'is_correct' => true],
                    ['answer' => 'Faqat statik metodlardan', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'protected xususiyatga kim kira oladi?',
                'answers'  => [
                    ['answer' => 'Faqat shu klass', 'is_correct' => false],
                    ['answer' => 'Hamma', 'is_correct' => false],
                    ['answer' => 'Shu klass va uning vorislari', 'is_correct' => true],
                    ['answer' => 'Faqat tashqi kodlar', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da klass metodiga qanday kiriladi?',
                'answers'  => [
                    ['answer' => 'klass::metod()', 'is_correct' => false],
                    ['answer' => 'klass.metod()', 'is_correct' => false],
                    ['answer' => '$obyekt->metod()', 'is_correct' => true],
                    ['answer' => 'klass->metod()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Statik metod qanday chaqiriladi?',
                'answers'  => [
                    ['answer' => '$obyekt->metod()', 'is_correct' => false],
                    ['answer' => 'metod()', 'is_correct' => false],
                    ['answer' => 'Klass::metod()', 'is_correct' => true],
                    ['answer' => 'new Klass->metod()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da interfeys yaratish uchun qaysi kalit so\'z ishlatiladi?',
                'answers'  => [
                    ['answer' => 'abstract', 'is_correct' => false],
                    ['answer' => 'type', 'is_correct' => false],
                    ['answer' => 'protocol', 'is_correct' => false],
                    ['answer' => 'interface', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'Trait nima?',
                'answers'  => [
                    ['answer' => 'Klass turi', 'is_correct' => false],
                    ['answer' => 'Bir nechta klasslarda qayta ishlatiluvchi metodlar to\'plami', 'is_correct' => true],
                    ['answer' => 'Interfeys turi', 'is_correct' => false],
                    ['answer' => 'Abstrakt klass', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da meros olish uchun qaysi kalit so\'z ishlatiladi?',
                'answers'  => [
                    ['answer' => 'inherits', 'is_correct' => false],
                    ['answer' => 'extends', 'is_correct' => true],
                    ['answer' => 'uses', 'is_correct' => false],
                    ['answer' => 'implements', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Abstrakt klass nima?',
                'answers'  => [
                    ['answer' => 'Interfeys bilan bir xil', 'is_correct' => false],
                    ['answer' => 'To\'liq implement qilinmagan, to\'g\'ridan-to\'g\'ri yaratolmaydigan klass', 'is_correct' => true],
                    ['answer' => 'Faqat statik metodlari bor klass', 'is_correct' => false],
                    ['answer' => 'Xususiyatlari yo\'q klass', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da parent:: nima uchun ishlatiladi?',
                'answers'  => [
                    ['answer' => 'Joriy klassga kirish', 'is_correct' => false],
                    ['answer' => 'Ota-klass metodi yoki xususiyatiga kirish', 'is_correct' => true],
                    ['answer' => 'Statik metodga kirish', 'is_correct' => false],
                    ['answer' => 'Interfeyslarga kirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Quyidagi kodda xato qayerda? class AI { private $key; } $ai = new AI(); echo $ai->key;',
                'answers'  => [
                    ['answer' => 'new AI() da', 'is_correct' => false],
                    ['answer' => 'class e\'lonida', 'is_correct' => false],
                    ['answer' => '$ai->key da — private xususiyatga tashqaridan kirish mumkin emas', 'is_correct' => true],
                    ['answer' => 'Xato yo\'q', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da __toString() metodi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Klassni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Obyekt matn sifatida ishlatilganda chaqiriladi', 'is_correct' => true],
                    ['answer' => 'Konstruktorni chaqiradi', 'is_correct' => false],
                    ['answer' => 'Destruktorni chaqiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da final kalit so\'zi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Faylni saqlaydi', 'is_correct' => false],
                    ['answer' => 'Klass meros olinmasin yoki metod qayta yozilmasin deb belgilaydi', 'is_correct' => true],
                    ['answer' => 'Konstantani e\'lon qiladi', 'is_correct' => false],
                    ['answer' => 'Metoddan chiqadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da const va static farqi nima?',
                'answers'  => [
                    ['answer' => 'Farq yo\'q', 'is_correct' => false],
                    ['answer' => 'const o\'zgarmaydi, static o\'zgarishi mumkin', 'is_correct' => true],
                    ['answer' => 'static o\'zgarmaydi, const o\'zgarishi mumkin', 'is_correct' => false],
                    ['answer' => 'Ikkalasi ham o\'zgarmas', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'OOP ning asosiy 4 ta prinsipi qaysilar?',
                'answers'  => [
                    ['answer' => 'Tezlik, Xotira, Xavfsizlik, Soddalik', 'is_correct' => false],
                    ['answer' => 'Inkapsulatsiya, Meros, Polimorfizm, Abstraksiya', 'is_correct' => true],
                    ['answer' => 'Sinf, Obyekt, Metod, Xususiyat', 'is_correct' => false],
                    ['answer' => 'public, private, protected, static', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Inkapsulatsiya (Encapsulation) nima?',
                'answers'  => [
                    ['answer' => 'Klass meros olishi', 'is_correct' => false],
                    ['answer' => 'Bir metod turli xil ishlashi', 'is_correct' => false],
                    ['answer' => 'Ma\'lumotlarni yashirish va faqat metodlar orqali kirish', 'is_correct' => true],
                    ['answer' => 'Klass yaratish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da ::class nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Klass obyektini', 'is_correct' => false],
                    ['answer' => 'Klassning to\'liq nomini (string)', 'is_correct' => true],
                    ['answer' => 'Klass metodlarini', 'is_correct' => false],
                    ['answer' => 'Klass xususiyatlarini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da instanceof operatori nima qiladi?',
                'answers'  => [
                    ['answer' => 'Yangi obyekt yaratadi', 'is_correct' => false],
                    ['answer' => 'Klass nomini qaytaradi', 'is_correct' => false],
                    ['answer' => 'Obyekt ma\'lum klassdan ekanligini tekshiradi', 'is_correct' => true],
                    ['answer' => 'Klassni o\'chiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da __destruct() qachon chaqiriladi?',
                'answers'  => [
                    ['answer' => 'Obyekt yaratilganda', 'is_correct' => false],
                    ['answer' => 'Obyekt yo\'q qilinganda yoki skript tugaganda', 'is_correct' => true],
                    ['answer' => 'Metod chaqirilganda', 'is_correct' => false],
                    ['answer' => 'Xato yuz berganda', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Polimorfizm (Polymorphism) nima?',
                'answers'  => [
                    ['answer' => 'Klasslar meros olishi', 'is_correct' => false],
                    ['answer' => 'Xususiyatlarni yashirish', 'is_correct' => false],
                    ['answer' => 'Bir metod turli klasslarda turli xil ishlashi', 'is_correct' => true],
                    ['answer' => 'Klass yaratish usuli', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da type hinting nima?',
                'answers'  => [
                    ['answer' => 'Kod izohi', 'is_correct' => false],
                    ['answer' => 'Parametr va qaytuvchi qiymat turini ko\'rsatish', 'is_correct' => true],
                    ['answer' => 'Xato xabari', 'is_correct' => false],
                    ['answer' => 'Klass nomi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'function savol(string $matn): string — bu nima?',
                'answers'  => [
                    ['answer' => 'Xato kod', 'is_correct' => false],
                    ['answer' => 'Parametr va qaytuvchi tur ko\'rsatilgan funksiya', 'is_correct' => true],
                    ['answer' => 'Klass metodi', 'is_correct' => false],
                    ['answer' => 'Abstrakt metod', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da use kalit so\'zi klass ichida nima qiladi?',
                'answers'  => [
                    ['answer' => 'Faylni ulaydi', 'is_correct' => false],
                    ['answer' => 'Namespaceni ulaydi', 'is_correct' => false],
                    ['answer' => 'Trait ni klassga ulaydi', 'is_correct' => true],
                    ['answer' => 'Interfeys implements qiladi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da namespace nima uchun?',
                'answers'  => [
                    ['answer' => 'Klass nomini qisqartirish', 'is_correct' => false],
                    ['answer' => 'Klass nomlar to\'qnashuvi oldini olish va kodni tashkil qilish', 'is_correct' => true],
                    ['answer' => 'Tezlikni oshirish', 'is_correct' => false],
                    ['answer' => 'Xavfsizlikni ta\'minlash', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Kompozitsiya (Composition) va Meros (Inheritance) farqi?',
                'answers'  => [
                    ['answer' => 'Farq yo\'q', 'is_correct' => false],
                    ['answer' => 'Kompozitsiya "has-a" (tarkibida bor), Meros "is-a" (turi)', 'is_correct' => true],
                    ['answer' => 'Meros tezroq', 'is_correct' => false],
                    ['answer' => 'Kompozitsiya OOP emas', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da clone kalit so\'zi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Klassni nusxalaydi', 'is_correct' => false],
                    ['answer' => 'Obyektning nusxasini yaratadi', 'is_correct' => true],
                    ['answer' => 'Klassni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Metodlarni nusxalaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da get_ va set_ metodlari (getter/setter) nima uchun?',
                'answers'  => [
                    ['answer' => 'Tezlikni oshirish uchun', 'is_correct' => false],
                    ['answer' => 'Private xususiyatlarga nazorat ostida kirish va o\'zgartirish uchun', 'is_correct' => true],
                    ['answer' => 'Klass nomini o\'zgartirish uchun', 'is_correct' => false],
                    ['answer' => 'Konstruktorni almashtirish uchun', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'JSON ning to\'liq nomi nima?',
                'answers'  => [
                    ['answer' => 'Java Standard Object Notation', 'is_correct' => false],
                    ['answer' => 'JavaScript Object Notation', 'is_correct' => true],
                    ['answer' => 'JSON Simple Object Network', 'is_correct' => false],
                    ['answer' => 'Java Script Open Node', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'CSV ning to\'liq nomi nima?',
                'answers'  => [
                    ['answer' => 'Character Separated Values', 'is_correct' => false],
                    ['answer' => 'Code Saved Variable', 'is_correct' => false],
                    ['answer' => 'Comma Separated Values', 'is_correct' => true],
                    ['answer' => 'Content Stored Values', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da faylga yozish uchun qaysi funksiya eng oddiy?',
                'answers'  => [
                    ['answer' => 'file_write()', 'is_correct' => false],
                    ['answer' => 'fwrite()', 'is_correct' => false],
                    ['answer' => 'file_put_contents()', 'is_correct' => true],
                    ['answer' => 'save_file()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da fayldan o\'qish uchun qaysi funksiya eng oddiy?',
                'answers'  => [
                    ['answer' => 'file_get_contents()', 'is_correct' => true],
                    ['answer' => 'file_read()', 'is_correct' => false],
                    ['answer' => 'fread_all()', 'is_correct' => false],
                    ['answer' => 'read_file()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'JSON_PRETTY_PRINT parametri nima qiladi?',
                'answers'  => [
                    ['answer' => 'JSON ni tezlashtiradi', 'is_correct' => false],
                    ['answer' => 'JSON ni o\'qilishi qulay formatda (bo\'sh joylar bilan) chiqaradi', 'is_correct' => true],
                    ['answer' => 'JSON ni kichraytiradi', 'is_correct' => false],
                    ['answer' => 'JSON ni tekshiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'JSON_UNESCAPED_UNICODE parametri nima uchun kerak?',
                'answers'  => [
                    ['answer' => 'Tez ishlash uchun', 'is_correct' => false],
                    ['answer' => 'O\'zbek/rus/arab harflarini to\'g\'ri saqlash uchun', 'is_correct' => true],
                    ['answer' => 'Faylni kichraytirish uchun', 'is_correct' => false],
                    ['answer' => 'Xavfsizlik uchun', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da fopen() funksiyasida \'r\' rejimi nima?',
                'answers'  => [
                    ['answer' => 'Faylga yozish', 'is_correct' => false],
                    ['answer' => 'Faylni o\'qish uchun ochish', 'is_correct' => true],
                    ['answer' => 'Faylni yaratish', 'is_correct' => false],
                    ['answer' => 'Faylni o\'chirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da fopen() funksiyasida \'w\' rejimi nima?',
                'answers'  => [
                    ['answer' => 'Faylni o\'qish', 'is_correct' => false],
                    ['answer' => 'Faylga yozish (mavjud bo\'lsa o\'chiradi)', 'is_correct' => true],
                    ['answer' => 'Faylni qo\'shish', 'is_correct' => false],
                    ['answer' => 'Faylni yaratish (faqat)', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da fopen() funksiyasida \'a\' rejimi nima?',
                'answers'  => [
                    ['answer' => 'Faylni o\'qish', 'is_correct' => false],
                    ['answer' => 'Faylni qayta yozish', 'is_correct' => false],
                    ['answer' => 'Faylning oxiriga qo\'shish', 'is_correct' => true],
                    ['answer' => 'Faylni arxivlash', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'CSV fayldan bitta qatorni o\'qish uchun qaysi funksiya?',
                'answers'  => [
                    ['answer' => 'csv_read()', 'is_correct' => false],
                    ['answer' => 'file_get_csv()', 'is_correct' => false],
                    ['answer' => 'fgetcsv()', 'is_correct' => true],
                    ['answer' => 'read_csv_line()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'CSV faylga bitta qator yozish uchun qaysi funksiya?',
                'answers'  => [
                    ['answer' => 'csv_write()', 'is_correct' => false],
                    ['answer' => 'file_put_csv()', 'is_correct' => false],
                    ['answer' => 'fputcsv()', 'is_correct' => true],
                    ['answer' => 'write_csv()', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'CSV fayldan o\'qishda sarlavhani o\'tkazish uchun nima qilish kerak?',
                'answers'  => [
                    ['answer' => 'skip_header() chaqirish', 'is_correct' => false],
                    ['answer' => 'fopen(\'r+\') ishlatish', 'is_correct' => false],
                    ['answer' => 'fgetcsv() ni bir marta chaqirish (natijani ishlatmasdan)', 'is_correct' => true],
                    ['answer' => 'header(false) parametri', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da file_put_contents() va fopen()/fwrite() farqi?',
                'answers'  => [
                    ['answer' => 'Farq yo\'q', 'is_correct' => false],
                    ['answer' => 'file_put_contents() soddaroq, fopen() ko\'proq nazorat beradi', 'is_correct' => true],
                    ['answer' => 'fopen() soddaroq', 'is_correct' => false],
                    ['answer' => 'file_put_contents() faqat JSON uchun', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da fclose() nima uchun kerak?',
                'answers'  => [
                    ['answer' => 'Faylni o\'chirish', 'is_correct' => false],
                    ['answer' => 'Ochilgan fayl deskriptorini yopish va resurslarni bo\'shatish', 'is_correct' => true],
                    ['answer' => 'Faylni saqlash', 'is_correct' => false],
                    ['answer' => 'Faylni ko\'chirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'JSON faylga o\'zbek harflari bilan saqlashda to\'g\'ri parametrlar?',
                'answers'  => [
                    ['answer' => 'JSON_ENCODE_UTF8', 'is_correct' => false],
                    ['answer' => 'JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE', 'is_correct' => true],
                    ['answer' => 'JSON_UNICODE', 'is_correct' => false],
                    ['answer' => 'JSON_UTF8 | JSON_FORMAT', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_sum() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massiv uzunligini qaytaradi', 'is_correct' => false],
                    ['answer' => 'Massiv elementlari yig\'indisini qaytaradi', 'is_correct' => true],
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                    ['answer' => 'Massivdagi maksimumni qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da count() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Massivdagi maksimal qiymat', 'is_correct' => false],
                    ['answer' => 'Massivlar yig\'indisi', 'is_correct' => false],
                    ['answer' => 'Massiv elementlari sonini', 'is_correct' => true],
                    ['answer' => 'Massivning birinchi elementi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da sort() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni teskarisiga buradi', 'is_correct' => false],
                    ['answer' => 'Massivni o\'sish tartibida saralaydi', 'is_correct' => true],
                    ['answer' => 'Massivni kamaytirish tartibida saralaydi', 'is_correct' => false],
                    ['answer' => 'Massivni alifbo tartibida saralaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_filter() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                    ['answer' => 'Massivni birlashtiradi', 'is_correct' => false],
                    ['answer' => 'Shartga mos elementlarni filtrlaydi', 'is_correct' => true],
                    ['answer' => 'Massivni o\'chiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_push() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massiv boshiga element qo\'shadi', 'is_correct' => false],
                    ['answer' => 'Massiv oxiriga element qo\'shadi', 'is_correct' => true],
                    ['answer' => 'Massivdan element o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Massivni birlashtiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da in_array() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni tekshiradi', 'is_correct' => false],
                    ['answer' => 'Massivda element borligini tekshiradi', 'is_correct' => true],
                    ['answer' => 'Element indeksini qaytaradi', 'is_correct' => false],
                    ['answer' => 'Massivni filtrlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_keys() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Massiv qiymatlarini', 'is_correct' => false],
                    ['answer' => 'Massiv kalitlarini (keys)', 'is_correct' => true],
                    ['answer' => 'Massiv uzunligini', 'is_correct' => false],
                    ['answer' => 'Massiv tipini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_values() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Massiv kalitlarini', 'is_correct' => false],
                    ['answer' => 'Massiv qiymatlarini (qayta indekslangan)', 'is_correct' => true],
                    ['answer' => 'Massiv o\'lchamini', 'is_correct' => false],
                    ['answer' => 'Massiv tipini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_merge() nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni filtrlaydi', 'is_correct' => false],
                    ['answer' => 'Ikki yoki undan ko\'p massivni birlashtiradi', 'is_correct' => true],
                    ['answer' => 'Massivlarni taqqoslaydi', 'is_correct' => false],
                    ['answer' => 'Massivning umumiy elementlarini qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da explode() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni birlashtiradi', 'is_correct' => false],
                    ['answer' => 'Matnni belgi bo\'yicha massivga bo\'ladi', 'is_correct' => true],
                    ['answer' => 'Massivni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Matnni formatlashtiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da implode() funksiyasi nima qiladi? (2)',
                'answers'  => [
                    ['answer' => 'Matnni bo\'ladi', 'is_correct' => false],
                    ['answer' => 'Massiv elementlarini birlashtirib matn qaytaradi', 'is_correct' => true],
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                    ['answer' => 'Massivni o\'chiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'CSV fayl formati qaysi dasturlarda ochiladi?',
                'answers'  => [
                    ['answer' => 'Faqat PHP da', 'is_correct' => false],
                    ['answer' => 'Excel, LibreOffice, Google Sheets va boshqa ko\'plab dasturlarda', 'is_correct' => true],
                    ['answer' => 'Faqat Python da', 'is_correct' => false],
                    ['answer' => 'Faqat text editorlarda', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da date() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Timestamp', 'is_correct' => false],
                    ['answer' => 'Formatlangan sana va vaqt', 'is_correct' => true],
                    ['answer' => 'Unix vaqti', 'is_correct' => false],
                    ['answer' => 'Sana ob\'ekti', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da time() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Formatlangan vaqt', 'is_correct' => false],
                    ['answer' => 'Sana ob\'ekti', 'is_correct' => false],
                    ['answer' => 'Unix timestamp (1970 yildan songi soniyalar soni)', 'is_correct' => true],
                    ['answer' => 'Vaqt zonasi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'AI ga ma\'lumot yuborishda normalizatsiya nima uchun kerak?',
                'answers'  => [
                    ['answer' => 'Ma\'lumotni kichraytirish', 'is_correct' => false],
                    ['answer' => 'AI kichik sonlar (0-1) bilan yaxshiroq ishlaydi', 'is_correct' => true],
                    ['answer' => 'Xatolarni kamaytirish', 'is_correct' => false],
                    ['answer' => 'Tezlikni oshirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da round() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sonni yaxlit qilib yuqoriga yaxlitlaydi', 'is_correct' => false],
                    ['answer' => 'Sonni yaxlit qilib pastga yaxlitlaydi', 'is_correct' => false],
                    ['answer' => 'Sonni ko\'rsatilgan aniqlikkacha yaxlitlaydi', 'is_correct' => true],
                    ['answer' => 'Sonning absolut qiymatini qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da min() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Massivdagi o\'rtacha qiymat', 'is_correct' => false],
                    ['answer' => 'Eng kichik qiymat', 'is_correct' => true],
                    ['answer' => 'Eng katta qiymat', 'is_correct' => false],
                    ['answer' => 'Yig\'indi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da max() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Eng kichik qiymat', 'is_correct' => false],
                    ['answer' => 'O\'rtacha qiymat', 'is_correct' => false],
                    ['answer' => 'Yig\'indi', 'is_correct' => false],
                    ['answer' => 'Eng katta qiymat', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'O\'rtacha (mean) hisoblash formulasi PHP da?',
                'answers'  => [
                    ['answer' => 'array_sum($arr) * count($arr)', 'is_correct' => false],
                    ['answer' => 'array_sum($arr) / count($arr)', 'is_correct' => true],
                    ['answer' => 'array_avg($arr)', 'is_correct' => false],
                    ['answer' => 'sum($arr) / len($arr)', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Sigmoid funksiyasi formulasi?',
                'answers'  => [
                    ['answer' => 'f(x) = max(0, x)', 'is_correct' => false],
                    ['answer' => 'f(x) = x / (1 + x)', 'is_correct' => false],
                    ['answer' => 'f(x) = 1 / (1 + e^(-x))', 'is_correct' => true],
                    ['answer' => 'f(x) = tanh(x)', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Sigmoid funksiyasi qanday qiymat oralig\'ini qaytaradi?',
                'answers'  => [
                    ['answer' => '-1 dan 1 gacha', 'is_correct' => false],
                    ['answer' => '0 dan 100 gacha', 'is_correct' => false],
                    ['answer' => '0 dan 1 gacha', 'is_correct' => true],
                    ['answer' => '-∞ dan +∞ gacha', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'ReLU funksiyasi formulasi?',
                'answers'  => [
                    ['answer' => 'f(x) = 1/(1+e^-x)', 'is_correct' => false],
                    ['answer' => 'f(x) = tanh(x)', 'is_correct' => false],
                    ['answer' => 'f(x) = x^2', 'is_correct' => false],
                    ['answer' => 'f(x) = max(0, x)', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'ReLU funksiyasi: relu(-5) = ?',
                'answers'  => [
                    ['answer' => '-5', 'is_correct' => false],
                    ['answer' => '5', 'is_correct' => false],
                    ['answer' => '0', 'is_correct' => true],
                    ['answer' => '0.5', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'ReLU funksiyasi: relu(3) = ?',
                'answers'  => [
                    ['answer' => '0', 'is_correct' => false],
                    ['answer' => '0.5', 'is_correct' => false],
                    ['answer' => '3', 'is_correct' => true],
                    ['answer' => '-3', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Tanh funksiyasi qanday qiymat oralig\'ini qaytaradi?',
                'answers'  => [
                    ['answer' => '0 dan 1 gacha', 'is_correct' => false],
                    ['answer' => '0 dan +∞ gacha', 'is_correct' => false],
                    ['answer' => '-1 dan 1 gacha', 'is_correct' => true],
                    ['answer' => '-∞ dan +∞ gacha', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Sigmoid funksiyasi qachon ishlatiladi?',
                'answers'  => [
                    ['answer' => 'Yashirin qatlamlarda', 'is_correct' => false],
                    ['answer' => 'Matritsa ko\'paytirishda', 'is_correct' => false],
                    ['answer' => 'Ikkilik klassifikatsiya (chiqish qatlami, 0-1 kerak bo\'lganda)', 'is_correct' => true],
                    ['answer' => 'Normalizatsiyada', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da exp() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'x ning kvadratini', 'is_correct' => false],
                    ['answer' => 'x ning logarifmini', 'is_correct' => false],
                    ['answer' => 'e ning x darajasini (e^x)', 'is_correct' => true],
                    ['answer' => 'x ning ildizini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da sqrt() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sonni kvadratlaydi', 'is_correct' => false],
                    ['answer' => 'Kvadrat ildiz hisoblaydi', 'is_correct' => true],
                    ['answer' => 'Kubik ildiz hisoblaydi', 'is_correct' => false],
                    ['answer' => 'Absolut qiymatni qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da abs() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sonni yaxlitlaydi', 'is_correct' => false],
                    ['answer' => 'Sonning absolut (musbat) qiymatini qaytaradi', 'is_correct' => true],
                    ['answer' => 'Sonni kvadratlaydi', 'is_correct' => false],
                    ['answer' => 'Sonni ikkilantiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Dispersiya (Variance) nima?',
                'answers'  => [
                    ['answer' => 'O\'rtacha qiymat', 'is_correct' => false],
                    ['answer' => 'O\'rtachadan chetlanishning o\'rtacha kvadrati', 'is_correct' => true],
                    ['answer' => 'Maksimal va minimal farqi', 'is_correct' => false],
                    ['answer' => 'Standart og\'ish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Standart og\'ish (Standard Deviation) qanday hisoblanadi?',
                'answers'  => [
                    ['answer' => 'Dispersiyani ikkilantirish bilan', 'is_correct' => false],
                    ['answer' => 'Dispersiyaning kvadrat ildizi bilan', 'is_correct' => true],
                    ['answer' => 'O\'rtachani ikkilantirish bilan', 'is_correct' => false],
                    ['answer' => 'Maksimal va minimal farqi bilan', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Normalizatsiya (Min-Max) formulasi?',
                'answers'  => [
                    ['answer' => '(x - o\'rtacha) / std', 'is_correct' => false],
                    ['answer' => 'x / maksimal', 'is_correct' => false],
                    ['answer' => '(x - minimal) * (maksimal - minimal)', 'is_correct' => false],
                    ['answer' => '(x - minimal) / (maksimal - minimal)', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'Neyron tarmoqda "og\'irlik" (weight) nima?',
                'answers'  => [
                    ['answer' => 'Neyronning balandligi', 'is_correct' => false],
                    ['answer' => 'Har bir kirishning muhimlik darajasini ko\'rsatuvchi son', 'is_correct' => true],
                    ['answer' => 'Neyronlar soni', 'is_correct' => false],
                    ['answer' => 'Qatlamlar soni', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Matritsa ko\'paytmasi A × B mumkin bo\'lishi uchun?',
                'answers'  => [
                    ['answer' => 'A va B bir xil o\'lchamda bo\'lishi kerak', 'is_correct' => false],
                    ['answer' => 'A ning ustun soni B ning qator soniga teng bo\'lishi kerak', 'is_correct' => true],
                    ['answer' => 'Faqat kvadrat matritsalar ko\'paytiriladi', 'is_correct' => false],
                    ['answer' => 'Hech qanday shart yo\'q', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Neyron tarmoqdagi "bias" nima?',
                'answers'  => [
                    ['answer' => 'Xato miqdori', 'is_correct' => false],
                    ['answer' => 'O\'rganish tezligi', 'is_correct' => false],
                    ['answer' => 'Qatlamlar soni', 'is_correct' => false],
                    ['answer' => 'Neyronning aktivatsiya chegarasini siljituvchi qo\'shimcha son', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'Leaky ReLU formulasi?',
                'answers'  => [
                    ['answer' => 'max(0, x)', 'is_correct' => false],
                    ['answer' => '1/(1+e^-x)', 'is_correct' => false],
                    ['answer' => 'x > 0 ? x : 0.01 * x', 'is_correct' => true],
                    ['answer' => 'tanh(x)', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da ** operatori nima qiladi?',
                'answers'  => [
                    ['answer' => 'Ko\'paytiradi', 'is_correct' => false],
                    ['answer' => 'Darajaga oshiradi (masalan, 2**3 = 8)', 'is_correct' => true],
                    ['answer' => 'Bo\'ladi', 'is_correct' => false],
                    ['answer' => 'Qoldiq hisoblaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da fmod() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Ikkita sonni ko\'paytiradi', 'is_correct' => false],
                    ['answer' => 'Haqiqiy sonlar uchun qoldiq hisoblaydi', 'is_correct' => true],
                    ['answer' => 'Sonni yaxlitlaydi', 'is_correct' => false],
                    ['answer' => 'Sonni formatlashtiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Neyron tarmoqning "kirish qatlami" nima?',
                'answers'  => [
                    ['answer' => 'Natijani chiqaradigan qatlam', 'is_correct' => false],
                    ['answer' => 'O\'rganishni boshqaradigan qatlam', 'is_correct' => false],
                    ['answer' => 'Ma\'lumotlar kiradigan qatlam', 'is_correct' => true],
                    ['answer' => 'Xatoni hisoblash qatlami', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Neyron tarmoqning "chiqish qatlami" nima?',
                'answers'  => [
                    ['answer' => 'Ma\'lumotlar kiradigan qatlam', 'is_correct' => false],
                    ['answer' => 'Yashirin qatlam', 'is_correct' => false],
                    ['answer' => 'Yakuniy natijani chiqaradigan qatlam', 'is_correct' => true],
                    ['answer' => 'O\'rganish qatlami', 'is_correct' => false],
                ],
            ],
            [
                'question' => '"Yashirin qatlam" (Hidden layer) nima?',
                'answers'  => [
                    ['answer' => 'Kirish qatlami', 'is_correct' => false],
                    ['answer' => 'Chiqish qatlami', 'is_correct' => false],
                    ['answer' => 'Kirish va chiqish qatlamlari orasidagi qatlamlar', 'is_correct' => true],
                    ['answer' => 'Biror qatlam emas', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'O\'rganish tezligi (Learning rate) nima?',
                'answers'  => [
                    ['answer' => 'Neyronlar soni', 'is_correct' => false],
                    ['answer' => 'Og\'irliklarni yangilash qanchalik katta qadamda bo\'lishini belgilaydi', 'is_correct' => true],
                    ['answer' => 'Qatlamlar soni', 'is_correct' => false],
                    ['answer' => 'Iteratsiya soni', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Epoch nima?',
                'answers'  => [
                    ['answer' => 'Bitta ma\'lumot qatori', 'is_correct' => false],
                    ['answer' => 'Butun o\'quv to\'plamidan bir marta o\'tish', 'is_correct' => true],
                    ['answer' => 'Neyron soni', 'is_correct' => false],
                    ['answer' => 'Og\'irliklar soni', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da pi() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => '3', 'is_correct' => false],
                    ['answer' => 'π soni (3.14159...)', 'is_correct' => true],
                    ['answer' => 'e soni', 'is_correct' => false],
                    ['answer' => '0', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da log() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sonni kvadratlaydi', 'is_correct' => false],
                    ['answer' => 'Tabiiy logarifm hisoblaydi', 'is_correct' => true],
                    ['answer' => '10 asosli logarifm', 'is_correct' => false],
                    ['answer' => '2 asosli logarifm', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da pow(2, 10) nima qaytaradi?',
                'answers'  => [
                    ['answer' => '20', 'is_correct' => false],
                    ['answer' => '200', 'is_correct' => false],
                    ['answer' => '1024', 'is_correct' => true],
                    ['answer' => '512', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Neyron tarmoqda "forward propagation" nima?',
                'answers'  => [
                    ['answer' => 'Xatoni orqaga tarqatish', 'is_correct' => false],
                    ['answer' => 'Kirishdan chiqishga qarab hisoblash', 'is_correct' => true],
                    ['answer' => 'Og\'irliklarni yangilash', 'is_correct' => false],
                    ['answer' => 'O\'rganish jarayoni', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Neyron tarmoqda "backpropagation" nima?',
                'answers'  => [
                    ['answer' => 'Kirishdan chiqishga hisoblash', 'is_correct' => false],
                    ['answer' => 'Xatoni orqaga tarqatib og\'irliklarni yangilash', 'is_correct' => true],
                    ['answer' => 'Ma\'lumot kiritish', 'is_correct' => false],
                    ['answer' => 'Natijani chiqarish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da intdiv() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Ikkita sonni ko\'paytiradi', 'is_correct' => false],
                    ['answer' => 'Butun sonli bo\'linma qaytaradi', 'is_correct' => true],
                    ['answer' => 'Qoldiq hisoblaydi', 'is_correct' => false],
                    ['answer' => 'Sonni yaxlitlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da is_numeric() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sonni formatlaydi', 'is_correct' => false],
                    ['answer' => 'Qiymat raqammi yoki raqamli matnmi tekshiradi', 'is_correct' => true],
                    ['answer' => 'Sonni butun qiladi', 'is_correct' => false],
                    ['answer' => 'Sonni qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Matritsa nima?',
                'answers'  => [
                    ['answer' => 'Bir o\'lchamli massiv', 'is_correct' => false],
                    ['answer' => 'Ikki o\'lchamli sonlar jadvali (qatorlar va ustunlar)', 'is_correct' => true],
                    ['answer' => 'Funksiyalar to\'plami', 'is_correct' => false],
                    ['answer' => 'O\'zgaruvchilar ro\'yxati', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_fill() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni tozalaydi', 'is_correct' => false],
                    ['answer' => 'Ko\'rsatilgan qiymat bilan massiv to\'ldiradi', 'is_correct' => true],
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                    ['answer' => 'Massivni kengaytiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'REST API ning to\'liq nomi?',
                'answers'  => [
                    ['answer' => 'Remote Execution Service Technology', 'is_correct' => false],
                    ['answer' => 'Representational State Transfer', 'is_correct' => true],
                    ['answer' => 'Request Execution Standard Technology', 'is_correct' => false],
                    ['answer' => 'Remote Standard Transfer', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP GET metodi nima uchun?',
                'answers'  => [
                    ['answer' => 'Ma\'lumot yuborish', 'is_correct' => false],
                    ['answer' => 'Ma\'lumot yangilash', 'is_correct' => false],
                    ['answer' => 'Ma\'lumot olish (o\'qish)', 'is_correct' => true],
                    ['answer' => 'Ma\'lumot o\'chirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP POST metodi nima uchun?',
                'answers'  => [
                    ['answer' => 'Ma\'lumot olish', 'is_correct' => false],
                    ['answer' => 'Ma\'lumot yuborish (yaratish)', 'is_correct' => true],
                    ['answer' => 'Ma\'lumot yangilash', 'is_correct' => false],
                    ['answer' => 'Ma\'lumot o\'chirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP PUT metodi nima uchun?',
                'answers'  => [
                    ['answer' => 'Ma\'lumot olish', 'is_correct' => false],
                    ['answer' => 'Ma\'lumot yuborish', 'is_correct' => false],
                    ['answer' => 'Ma\'lumotni to\'liq yangilash', 'is_correct' => true],
                    ['answer' => 'Ma\'lumot o\'chirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP DELETE metodi nima uchun?',
                'answers'  => [
                    ['answer' => 'Ma\'lumot olish', 'is_correct' => false],
                    ['answer' => 'Ma\'lumot yaratish', 'is_correct' => false],
                    ['answer' => 'Ma\'lumot yangilash', 'is_correct' => false],
                    ['answer' => 'Ma\'lumotni o\'chirish', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'HTTP 200 status kodi nima degani?',
                'answers'  => [
                    ['answer' => 'Yaratildi', 'is_correct' => false],
                    ['answer' => 'Topilmadi', 'is_correct' => false],
                    ['answer' => 'Server xatosi', 'is_correct' => false],
                    ['answer' => 'So\'rov muvaffaqiyatli bajarildi', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'HTTP 201 status kodi nima degani?',
                'answers'  => [
                    ['answer' => 'OK', 'is_correct' => false],
                    ['answer' => 'Yangi resurs muvaffaqiyatli yaratildi', 'is_correct' => true],
                    ['answer' => 'Topilmadi', 'is_correct' => false],
                    ['answer' => 'Ruxsat yo\'q', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP 400 status kodi nima degani?',
                'answers'  => [
                    ['answer' => 'Muvaffaqiyatli', 'is_correct' => false],
                    ['answer' => 'So\'rov noto\'g\'ri formatda (Bad Request)', 'is_correct' => true],
                    ['answer' => 'Ruxsat yo\'q', 'is_correct' => false],
                    ['answer' => 'Server xatosi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP 401 status kodi nima degani?',
                'answers'  => [
                    ['answer' => 'So\'rov noto\'g\'ri', 'is_correct' => false],
                    ['answer' => 'Topilmadi', 'is_correct' => false],
                    ['answer' => 'Ruxsat yo\'q — autentifikatsiya kerak', 'is_correct' => true],
                    ['answer' => 'Server xatosi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP 404 status kodi nima degani?',
                'answers'  => [
                    ['answer' => 'Muvaffaqiyatli', 'is_correct' => false],
                    ['answer' => 'Ruxsat yo\'q', 'is_correct' => false],
                    ['answer' => 'So\'ralgan resurs topilmadi', 'is_correct' => true],
                    ['answer' => 'Server xatosi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP 429 status kodi nima degani?',
                'answers'  => [
                    ['answer' => 'Server xatosi', 'is_correct' => false],
                    ['answer' => 'Ruxsat yo\'q', 'is_correct' => false],
                    ['answer' => 'Juda ko\'p so\'rov yuborildi', 'is_correct' => true],
                    ['answer' => 'Topilmadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTTP 500 status kodi nima degani?',
                'answers'  => [
                    ['answer' => 'Muvaffaqiyatli', 'is_correct' => false],
                    ['answer' => 'Topilmadi', 'is_correct' => false],
                    ['answer' => 'Ruxsat yo\'q', 'is_correct' => false],
                    ['answer' => 'Server ichki xatosi', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'curl_error() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'HTTP status kodi', 'is_correct' => false],
                    ['answer' => 'Javob matnini', 'is_correct' => false],
                    ['answer' => 'cURL ning o\'z xato xabarini', 'is_correct' => true],
                    ['answer' => 'Serverning xato xabarini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Temperature parametri 0.0 bo\'lganda AI javobi qanday?',
                'answers'  => [
                    ['answer' => 'Xato qaytaradi', 'is_correct' => false],
                    ['answer' => 'Javob kelmaydi', 'is_correct' => false],
                    ['answer' => 'Eng aniq va takrorlanuvchi javob', 'is_correct' => true],
                    ['answer' => 'Eng ijodiy javob', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Temperature parametri 2.0 bo\'lganda AI javobi qanday?',
                'answers'  => [
                    ['answer' => 'Eng aniq javob', 'is_correct' => false],
                    ['answer' => 'Juda tasodifiy va ijodiy javob', 'is_correct' => true],
                    ['answer' => 'Javob kelmaydi', 'is_correct' => false],
                    ['answer' => 'Xato chiqadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'top_p parametri nima uchun?',
                'answers'  => [
                    ['answer' => 'Javob uzunligini belgilash', 'is_correct' => false],
                    ['answer' => 'So\'z tanlash xilma-xilligini nazorat qilish', 'is_correct' => true],
                    ['answer' => 'Model tanlash', 'is_correct' => false],
                    ['answer' => 'Til belgilash', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq da llama-3.1-8b-instant modeli qanday?',
                'answers'  => [
                    ['answer' => 'Eng kuchli model', 'is_correct' => false],
                    ['answer' => 'Tez ishlaydi, oddiy savollar uchun', 'is_correct' => true],
                    ['answer' => 'Uzun matnlar uchun', 'is_correct' => false],
                    ['answer' => 'Ko\'p tilli model', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da microtime(true) nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Butun sonli vaqt', 'is_correct' => false],
                    ['answer' => 'Millisekundli aniqlikda Unix timestamp', 'is_correct' => true],
                    ['answer' => 'Formatlangan vaqt', 'is_correct' => false],
                    ['answer' => 'Vaqt ob\'ekti', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'API so\'rovida Content-Type: application/json headeri nima ko\'rsatadi?',
                'answers'  => [
                    ['answer' => 'Javob formati', 'is_correct' => false],
                    ['answer' => 'Yuborilayotgan ma\'lumot JSON formatida ekanligini', 'is_correct' => true],
                    ['answer' => 'API versiyasi', 'is_correct' => false],
                    ['answer' => 'Autentifikatsiya turi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_merge() va + operatori farqi?',
                'answers'  => [
                    ['answer' => 'Farq yo\'q', 'is_correct' => false],
                    ['answer' => 'array_merge() kalitlarni qayta indekslaydi, + operatori asl kalitlarni saqlaydi', 'is_correct' => true],
                    ['answer' => '+ operatori tezroq', 'is_correct' => false],
                    ['answer' => 'array_merge() faqat raqamli massivlar uchun', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da match(true) konstruksiyasi nima?',
                'answers'  => [
                    ['answer' => 'Faqat true qiymatini tekshiradi', 'is_correct' => false],
                    ['answer' => 'Har bir shartni ketma-ket tekshirib mos keladigan qiymatni qaytaradi', 'is_correct' => true],
                    ['answer' => 'switch() ning nomi', 'is_correct' => false],
                    ['answer' => 'Tsikl konstruksiyasi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Groq API da stream: true parametri nima qiladi?',
                'answers'  => [
                    ['answer' => 'Tezroq ishlaydi', 'is_correct' => false],
                    ['answer' => 'Javobni bo\'laklab (streaming) yuboradi', 'is_correct' => true],
                    ['answer' => 'Xatolarni ko\'rsatadi', 'is_correct' => false],
                    ['answer' => 'Modelni o\'zgartiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da curl_setopt_array() nima qiladi?',
                'answers'  => [
                    ['answer' => 'Bitta parametr o\'rnatadi', 'is_correct' => false],
                    ['answer' => 'Bir vaqtda ko\'plab cURL parametrlarini o\'rnatadi', 'is_correct' => true],
                    ['answer' => 'Parametrlarni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Parametrlarni qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'REST API da endpoint nima?',
                'answers'  => [
                    ['answer' => 'API kaliti', 'is_correct' => false],
                    ['answer' => 'Ma\'lum resurslarga kirish uchun URL manzil', 'is_correct' => true],
                    ['answer' => 'Javob formati', 'is_correct' => false],
                    ['answer' => 'HTTP metodi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da http_build_query() nima qiladi?',
                'answers'  => [
                    ['answer' => 'URL tekshiradi', 'is_correct' => false],
                    ['answer' => 'Massivdan URL query string yaratadi', 'is_correct' => true],
                    ['answer' => 'URL parchalaydi', 'is_correct' => false],
                    ['answer' => 'So\'rov yuboradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da parse_url() nima qiladi?',
                'answers'  => [
                    ['answer' => 'URL yaratadi', 'is_correct' => false],
                    ['answer' => 'URL yuboradi', 'is_correct' => false],
                    ['answer' => 'URL ni qismlariga ajratadi (host, path, query va h.k.)', 'is_correct' => true],
                    ['answer' => 'URL ni kodlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da urlencode() nima qiladi?',
                'answers'  => [
                    ['answer' => 'URL ochadi', 'is_correct' => false],
                    ['answer' => 'URL tekshiradi', 'is_correct' => false],
                    ['answer' => 'URL da ishlatiladigan xavfsiz formatga o\'tkazadi', 'is_correct' => true],
                    ['answer' => 'URL yaratadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da header() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sahifaga sarlavha qo\'shadi', 'is_correct' => false],
                    ['answer' => 'HTTP response header yuboradi', 'is_correct' => true],
                    ['answer' => 'Forma yaratadi', 'is_correct' => false],
                    ['answer' => 'Cookie saqlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da ob_start() va ob_get_clean() nima uchun?',
                'answers'  => [
                    ['answer' => 'Xatolarni yashirish', 'is_correct' => false],
                    ['answer' => 'Chiqishni buferga yig\'ish va keyinroq ishlatish', 'is_correct' => true],
                    ['answer' => 'Sessiyani boshlash', 'is_correct' => false],
                    ['answer' => 'Cookie o\'qish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da die() va exit() farqi?',
                'answers'  => [
                    ['answer' => 'die() xato, exit() to\'g\'ri', 'is_correct' => false],
                    ['answer' => 'Farq yo\'q — ikkalasi ham skriptni to\'xtatadi', 'is_correct' => true],
                    ['answer' => 'die() xabar chiqaradi, exit() chiqarmaydi', 'is_correct' => false],
                    ['answer' => 'exit() tezroq ishlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da try-catch nima uchun?',
                'answers'  => [
                    ['answer' => 'Kod tezlashtirish', 'is_correct' => false],
                    ['answer' => 'Xatolarni tutib qayta ishlash (exception handling)', 'is_correct' => true],
                    ['answer' => 'Funksiya yaratish', 'is_correct' => false],
                    ['answer' => 'Tsikl yaratish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da throw new Exception() nima qiladi?',
                'answers'  => [
                    ['answer' => 'Xatoni e\'tiborsiz qoldiradi', 'is_correct' => false],
                    ['answer' => 'Yangi istisno (exception) otadi', 'is_correct' => true],
                    ['answer' => 'Xatoni yashiradi', 'is_correct' => false],
                    ['answer' => 'Dasturni to\'xtatadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da finally bloki qachon bajariladi?',
                'answers'  => [
                    ['answer' => 'Faqat xato bo\'lganda', 'is_correct' => false],
                    ['answer' => 'Faqat xato bo\'lmaganda', 'is_correct' => false],
                    ['answer' => 'try-catch dan qat\'i nazar har doim', 'is_correct' => true],
                    ['answer' => 'Faqat catch bloki bo\'lganda', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTML formadan PHP ga ma\'lumot yuborish uchun qaysi metod xavfsizroq?',
                'answers'  => [
                    ['answer' => 'GET', 'is_correct' => false],
                    ['answer' => 'POST', 'is_correct' => true],
                    ['answer' => 'PUT', 'is_correct' => false],
                    ['answer' => 'REQUEST', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTML formada method="get" bo\'lsa ma\'lumot qayerda ko\'rinadi?',
                'answers'  => [
                    ['answer' => 'Cookie da', 'is_correct' => false],
                    ['answer' => 'Session da', 'is_correct' => false],
                    ['answer' => 'URL da (query string)', 'is_correct' => true],
                    ['answer' => 'Header da', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da $_POST superglobali nima?',
                'answers'  => [
                    ['answer' => 'GET so\'rovlarini saqlaydi', 'is_correct' => false],
                    ['answer' => 'POST metodi orqali yuborilgan forma ma\'lumotlarini saqlaydi', 'is_correct' => true],
                    ['answer' => 'Session ma\'lumotlarini saqlaydi', 'is_correct' => false],
                    ['answer' => 'Cookie ma\'lumotlarini saqlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da $_SERVER[\'REQUEST_METHOD\'] nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Serverning IP manzilini', 'is_correct' => false],
                    ['answer' => 'HTTP so\'rov metodini (GET yoki POST)', 'is_correct' => true],
                    ['answer' => 'Sahifa URL ini', 'is_correct' => false],
                    ['answer' => 'Foydalanuvchi IP ini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da htmlspecialchars() nima uchun?',
                'answers'  => [
                    ['answer' => 'HTML ni chiroyli qilish', 'is_correct' => false],
                    ['answer' => 'XSS hujumlardan himoya — HTML maxsus belgilarni escape qilish', 'is_correct' => true],
                    ['answer' => 'CSS qo\'shish', 'is_correct' => false],
                    ['answer' => 'JavaScript o\'chirish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'XSS hujumi nima?',
                'answers'  => [
                    ['answer' => 'Serverga haddan ortiq so\'rov yuborish', 'is_correct' => false],
                    ['answer' => 'Saytga zararli JavaScript kodi kiritish', 'is_correct' => true],
                    ['answer' => 'Parolni topish urinishi', 'is_correct' => false],
                    ['answer' => 'API kalitini o\'g\'irlash', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da session_start() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Yangi session yaratadi', 'is_correct' => false],
                    ['answer' => 'Session ni boshlaydi yoki mavjudini davom ettiradi', 'is_correct' => true],
                    ['answer' => 'Session ni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Session ID ni qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da $_SESSION superglobali nima?',
                'answers'  => [
                    ['answer' => 'Cookie ma\'lumotlari', 'is_correct' => false],
                    ['answer' => 'POST ma\'lumotlari', 'is_correct' => false],
                    ['answer' => 'Server tomonida saqlanadigan session ma\'lumotlari', 'is_correct' => true],
                    ['answer' => 'GET ma\'lumotlari', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Session va Cookie asosiy farqi?',
                'answers'  => [
                    ['answer' => 'Farq yo\'q', 'is_correct' => false],
                    ['answer' => 'Session serverda, Cookie foydalanuvchi brauzerida saqlanadi', 'is_correct' => true],
                    ['answer' => 'Cookie serverda, Session brauzerda', 'is_correct' => false],
                    ['answer' => 'Ikkalasi ham brauzerda', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da session_destroy() nima qiladi?',
                'answers'  => [
                    ['answer' => 'Session faylini o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Joriy session ma\'lumotlarini to\'liq o\'chiradi', 'is_correct' => true],
                    ['answer' => 'Faqat bitta session o\'zgaruvchisini o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Brauzer cookie ini o\'chiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da unset($_SESSION[\'kalit\']) nima qiladi?',
                'answers'  => [
                    ['answer' => 'Butun session o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Session dan faqat bir kalit o\'zgaruvchisini o\'chiradi', 'is_correct' => true],
                    ['answer' => 'Session ni to\'xtatadi', 'is_correct' => false],
                    ['answer' => 'Session faylini o\'chiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da setcookie() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Session yaratadi', 'is_correct' => false],
                    ['answer' => 'Foydalanuvchi brauzeriga Cookie saqlaydi', 'is_correct' => true],
                    ['answer' => 'POST ma\'lumot yuboradi', 'is_correct' => false],
                    ['answer' => 'Header yuboradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da $_COOKIE superglobali nima?',
                'answers'  => [
                    ['answer' => 'Session ma\'lumotlari', 'is_correct' => false],
                    ['answer' => 'POST ma\'lumotlari', 'is_correct' => false],
                    ['answer' => 'Brauzerdan kelgan Cookie ma\'lumotlari', 'is_correct' => true],
                    ['answer' => 'Server ma\'lumotlari', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da header(\'Location: /sahifa.php\') nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sahifa sarlavhasini o\'zgartiradi', 'is_correct' => false],
                    ['answer' => 'Foydalanuvchini boshqa sahifaga yo\'naltiradi (redirect)', 'is_correct' => true],
                    ['answer' => 'Sahifani yangilaydi', 'is_correct' => false],
                    ['answer' => 'HTTP status qo\'shadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da form validation nima uchun muhim?',
                'answers'  => [
                    ['answer' => 'Sahifani chiroyli qilish', 'is_correct' => false],
                    ['answer' => 'Noto\'g\'ri yoki zararli ma\'lumotlar kirishini oldini olish', 'is_correct' => true],
                    ['answer' => 'Tezlikni oshirish', 'is_correct' => false],
                    ['answer' => 'Session boshqarish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da empty() funksiyasi nima tekshiradi?',
                'answers'  => [
                    ['answer' => 'O\'zgaruvchi aniqlanganmi', 'is_correct' => false],
                    ['answer' => 'O\'zgaruvchi bo\'sh, null, 0 yoki false ekanligini', 'is_correct' => true],
                    ['answer' => 'O\'zgaruvchi tipini', 'is_correct' => false],
                    ['answer' => 'O\'zgaruvchi uzunligini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da isset() funksiyasi nima tekshiradi?',
                'answers'  => [
                    ['answer' => 'O\'zgaruvchi bo\'shmi', 'is_correct' => false],
                    ['answer' => 'O\'zgaruvchi aniqlanganmi va null emasmi', 'is_correct' => true],
                    ['answer' => 'O\'zgaruvchi tipini', 'is_correct' => false],
                    ['answer' => 'O\'zgaruvchi qiymatini', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'CSS da linear-gradient() nima qiladi?',
                'answers'  => [
                    ['answer' => 'Rasm qo\'shadi', 'is_correct' => false],
                    ['answer' => 'Ikki yoki undan ko\'p rang orasida gradyent fon yaratadi', 'is_correct' => true],
                    ['answer' => 'Animatsiya qo\'shadi', 'is_correct' => false],
                    ['answer' => 'Rang o\'zgartiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTML da textarea elementi nima uchun?',
                'answers'  => [
                    ['answer' => 'Bitta qatorli matn kiritish', 'is_correct' => false],
                    ['answer' => 'Ko\'p qatorli matn kiritish', 'is_correct' => true],
                    ['answer' => 'Fayl yuklash', 'is_correct' => false],
                    ['answer' => 'Tugma yaratish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'HTML da select elementi nima uchun?',
                'answers'  => [
                    ['answer' => 'Matn kiritish', 'is_correct' => false],
                    ['answer' => 'Ro\'yxatdan tanlash (dropdown)', 'is_correct' => true],
                    ['answer' => 'Fayl yuklash', 'is_correct' => false],
                    ['answer' => 'Tugma yaratish', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da nl2br() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'HTML ni tozalaydi', 'is_correct' => false],
                    ['answer' => '\n (yangi qator) belgisini HTML <br> ga aylantiradi', 'is_correct' => true],
                    ['answer' => 'Matnni formatlaydi', 'is_correct' => false],
                    ['answer' => 'HTML ni tekshiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da strip_tags() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'CSS stillarni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'HTML va PHP teglarini matndan olib tashlaydi', 'is_correct' => true],
                    ['answer' => 'JavaScript o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Bo\'sh joylarni o\'chiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da strlen() funksiyasi nima qaytaradi?',
                'answers'  => [
                    ['answer' => 'Matnning birinchi belgisini', 'is_correct' => false],
                    ['answer' => 'Matn uzunligini (belgilar soni)', 'is_correct' => true],
                    ['answer' => 'Matnning oxirgi belgisini', 'is_correct' => false],
                    ['answer' => 'Matnni qaytaradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da strtolower() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Matnni katta harfga aylantiradi', 'is_correct' => false],
                    ['answer' => 'Matnni kichik harfga aylantiradi', 'is_correct' => true],
                    ['answer' => 'Matnni tozalaydi', 'is_correct' => false],
                    ['answer' => 'Matnni saralaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da strtoupper() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Matnni katta harfga aylantiradi', 'is_correct' => true],
                    ['answer' => 'Matnni kichik harfga aylantiradi', 'is_correct' => false],
                    ['answer' => 'Matnni tozalaydi', 'is_correct' => false],
                    ['answer' => 'Matnni kesadi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da str_replace() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Matnda qidiradi', 'is_correct' => false],
                    ['answer' => 'Matndagi belgilarni boshqasiga almashtiradi', 'is_correct' => true],
                    ['answer' => 'Matnni bo\'ladi', 'is_correct' => false],
                    ['answer' => 'Matnni birlashtiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da substr() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Matnni kattalashtiradi', 'is_correct' => false],
                    ['answer' => 'Matnning bir qismini qaytaradi', 'is_correct' => true],
                    ['answer' => 'Matnni qidiradi', 'is_correct' => false],
                    ['answer' => 'Matnni tozalaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da strpos() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Matnni kesadi', 'is_correct' => false],
                    ['answer' => 'Matnda qidiriladigan matnning pozitsiyasini qaytaradi', 'is_correct' => true],
                    ['answer' => 'Matnni almashtiradi', 'is_correct' => false],
                    ['answer' => 'Matnni birlashtiradi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da number_format() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Sonni yaxlitlaydi', 'is_correct' => false],
                    ['answer' => 'Sonni formatlangan ko\'rinishda chiqaradi (minglik ajratgich va h.k.)', 'is_correct' => true],
                    ['answer' => 'Sonni tekshiradi', 'is_correct' => false],
                    ['answer' => 'Sonni saralaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da array_unique() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                    ['answer' => 'Massivdan takroriy qiymatlarni olib tashlaydi', 'is_correct' => true],
                    ['answer' => 'Massivni birlashtiradi', 'is_correct' => false],
                    ['answer' => 'Massivni filtrlaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da compact() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Faylni siqadi', 'is_correct' => false],
                    ['answer' => 'O\'zgaruvchilardan assotsiativ massiv yaratadi', 'is_correct' => true],
                    ['answer' => 'Massivni siqadi', 'is_correct' => false],
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da extract() funksiyasi nima qiladi?',
                'answers'  => [
                    ['answer' => 'Fayldan o\'qiydi', 'is_correct' => false],
                    ['answer' => 'Assotsiativ massiv kalitlaridan o\'zgaruvchilar yaratadi', 'is_correct' => true],
                    ['answer' => 'Massivni filtrlaydi', 'is_correct' => false],
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da list() yoki [] destructuring nima qiladi?',
                'answers'  => [
                    ['answer' => 'Massiv yaratadi', 'is_correct' => false],
                    ['answer' => 'Massiv elementlarini o\'zgaruvchilarga ajratib oladi', 'is_correct' => true],
                    ['answer' => 'Massivni o\'chiradi', 'is_correct' => false],
                    ['answer' => 'Massivni saralaydi', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'PHP da ob_start() va ob_get_clean() bilan AI javobini template ga qo\'yish nima uchun qulay?',
                'answers'  => [
                    ['answer' => 'Kod tezlashadi', 'is_correct' => false],
                    ['answer' => 'PHP chiqishini string sifatida saqlash va boshqa joyda ishlatish mumkin', 'is_correct' => true],
                    ['answer' => 'Xatolar kamayadi', 'is_correct' => false],
                    ['answer' => 'Sessiya tezlashadi', 'is_correct' => false],
                ],
            ],
        ];
    }
}
