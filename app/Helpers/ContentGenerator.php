<?php
namespace App\Helpers;

class ContentGenerator
{
    // Load banks
    protected static function loadBank2()
    {
        $path = __DIR__ . '/../Data/content_bank2.php';
        if (file_exists($path)) return include $path;
        return [];
    }
    protected static function loadBank1()
    {
        $path = __DIR__ . '/../Data/content_bank1.php';
        if (file_exists($path)) return include $path;
        return [];
    }

    public static function generateModelContent(string $brandSlug, string $modelSlug, int $targetWords = 1200): array
    {
        $bank2 = self::loadBank2();
        $bank1 = self::loadBank1();
        $brand = $bank2[$brandSlug] ?? null;
        $model = $brand['models'][$modelSlug] ?? null;

        $title = $model['name'] ?? ucfirst($modelSlug);
        $intro = "معرفی و چارچوب نگهداری برای " . $title . ". این صفحه مرجعِ علائم، مشکلات رایج، خدمات اولویت‌دار و راهکارهای نگهداری برای " . $title . " است. اطلاعات ترکیبی از پایگاه داده خدمات و تجربهٔ تعمیرگاهی استخراج شده است.";

        $sections = [];
        $sections[] = ['heading' => 'چرا این مدل اهمیت دارد', 'body' => $model['note'] ?? ($brand['name'] ?? '')];

        // Priority services
        $priority = $model['priority_services'] ?? [];
        $psHtml = '';
        if (!empty($priority)) {
            $psHtml .= "<ol>";
            foreach ($priority as $p) {
                $psHtml .= "<li><strong>" . htmlspecialchars($p['title'] ?? $p['slug']) . "</strong> — " . htmlspecialchars($p['reason'] ?? '') . "</li>";
            }
            $psHtml .= "</ol>";
            $sections[] = ['heading' => 'خدمات اولویت‌دار', 'body' => $psHtml];
        }

        // Common problems
        $problems = [
            'صدای غیرطبیعی در دور آرام یا هنگام شتاب‌گیری',
            'مصرف غیرمعمول روغن یا کاهش فشار روغن',
            'نشتی مایعات خنک‌کننده یا کاهش سریع دمای رادیاتور',
            'لرزش یا تقه در گیربکس هنگام تعویض دنده',
            'سوء‌عملکرد سیستم برق و روشن نشدن در شرایط خاص'
        ];
        $probHtml = "<ul>";
        foreach ($problems as $pr) $probHtml .= "<li>" . htmlspecialchars($pr) . "</li>";
        $probHtml .= "</ul>";
        $sections[] = ['heading' => '۵ مشکل و نشانهٔ رایج', 'body' => $probHtml];

        // Troubleshooting steps
        $steps = "برای تشخیص صحیح ابتدا داده‌های دیاگ را ثبت کنید، سپس تست‌های الکتریکی (ولتاژ باتری، تست دینام)، تست فشار روغن و بررسی چشمی سیم‌کشی و اتصالات انجام شود. در موارد نشتی، مسیرِ جریان مایع دنبال و ریشه‌یابی شود.";
        $sections[] = ['heading' => 'مسیر عیب‌یابی پیشنهادی', 'body' => $steps];

        // Maintenance tips
        $tips = "نکات نگهداری: تعویض به‌موقع روغن و فیلترها، بازدید دوره‌ای سیستم خنک‌کننده، پایش وضعیت تسمه‌ها و شلنگ‌ها، بررسی اتصال‌های باتری و اطمینان از کارکرد صحیح سیستم شارژ.";
        $sections[] = ['heading' => 'نکات نگهداری', 'body' => $tips];

        // CTA
        $cta = "اگر علامت مشکوک دیدید یا خواستید سرویس دقیق‌تری انجام شود، <a href='" . SITE_URL . "/booking'>همین حالا رزرو کنید</a> یا به نزدیک‌ترین تعمیرگاه مراجعه نمایید.";
        $sections[] = ['heading' => 'اقدام بعدی', 'body' => $cta];

        // FAQs (generate 5)
        $faqs = [];
        $faqs[] = ['q' => 'هر چند وقت یک‌بار باید سرویس شود؟', 'a' => 'معمولاً هر 10 تا 15 هزار کیلومتر یا سالی یک‌بار، اما بسته به کارکرد و شرایط رانندگی ممکن است زودتر نیاز باشد.'];
        $faqs[] = ['q' => 'اگر بوی سوختگی یا دود مشاهده شد چه کنم؟', 'a' => 'سریع خودرو را متوقف، از لحاظ ایمنی بررسی و به تعمیرگاه مراجعه کنید؛ ممکن است ربطی به گیربکس یا کلاچ داشته باشد.'];
        $faqs[] = ['q' => 'آیا تعمیر موتور در این مدل رایج است؟', 'a' => 'برخی موتورها به واشر سرسیلندر یا مصرف روغن حساس‌اند؛ با سرویس منظم ریسک کاهش می‌یابد.'];
        $faqs[] = ['q' => 'آیا نصب قطعه‌ی پسازمانی تأثیر دارد؟', 'a' => 'قطعات غیرمجاز ممکن است عملکرد و ایمنی را کاهش دهند؛ از قطعات معتبر و با گارانتی استفاده کنید.'];
        $faqs[] = ['q' => 'آیا می‌توانم خودم برخی تعمیرات جزئی را انجام دهم؟', 'a' => 'بعضی نگهداری‌ها مانند تعویض فیلتر هوا یا روغن ساده است، اما برای عیب‌یابی الکترونیکی یا گیربکس به متخصص نیاز دارید.'];

        // Build HTML
        $html = "";
        $html .= "<div class=\"model-content\">";
        $html .= "<p>" . htmlspecialchars($intro) . "</p>";
        foreach ($sections as $s) {
            $html .= "<h3>" . htmlspecialchars($s['heading']) . "</h3>";
            $html .= "<div class=\"section-body\">" . $s['body'] . "</div>";
        }

        // Expand until target word count approximately reached
        $currentWords = str_word_count(strip_tags($html));
        $fillParagraph = "این بخش تکمیلی برای تکمیل راهنمای نگهداری و راهکارهای عملیاتی است. رعایت نکات فوق و مراجعه به متخصص در موارد نگران‌کننده از ضروریات جلوگیری از آسیب‌های پرهزینه است.";
        while ($currentWords < $targetWords) {
            $html .= "<p>" . htmlspecialchars($fillParagraph) . "</p>";
            $currentWords = str_word_count(strip_tags($html));
            // safety cap to avoid infinite loop
            if ($currentWords > ($targetWords + 5000)) break;
        }
        $html .= "</div>";

        // Build FAQ schema array
        $faqSchema = [];
        foreach ($faqs as $f) {
            $faqSchema[] = ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]];
        }

        return ['title' => $title, 'html' => $html, 'faq' => $faqs, 'faqSchema' => $faqSchema];
    }

    public static function generateMatrixContent(string $serviceSlug, string $brandSlug, string $modelSlug, int $targetWords = 1000): array
    {
        // minimal generator combining bank1 and bank2 facts
        $bank2 = self::loadBank2();
        $bank1 = self::loadBank1();
        $brand = $bank2[$brandSlug] ?? null;
        $model = $brand['models'][$modelSlug] ?? null;
        $service = $bank1[$serviceSlug] ?? null;

        $title = ($service['why'] ?? ucfirst($serviceSlug)) . ' برای ' . ($model['name'] ?? $modelSlug);
        $intro = 'راهنمای ترکیبی برای ' . ($service['why'] ?? $serviceSlug) . ' در مدل ' . ($model['name'] ?? $modelSlug) . '. این صفحه علایم، علت‌های محتمل و اقدام‌های تعمیرگاهی را به‌صورت ماتریس‌وار فهرست می‌کند.';

        $html = '<div class="matrix-content"><p>' . htmlspecialchars($intro) . '</p>';
        $html .= '<h3>جدول علائم، علت محتمل و اقدام تعمیرگاهی</h3>';
        $html .= '<div class="table-wrap"><table><thead><tr><th>علامت</th><th>علت محتمل</th><th>اقدام تعمیرگاهی</th></tr></thead><tbody>';

        $rows = [
            ['علامت' => 'صوت یا تقه در حرکت', 'علت' => 'بوش یا قطعات گیربکس/تعلیق فرسوده', 'اقدام' => 'بازرسی و تعویض قطعات فرسوده'],
            ['علامت' => 'نشتی مایعات', 'علت' => 'واشر یا شلنگ معیوب', 'اقدام' => 'عیب‌یابی مسیر نشتی و تعویض قطعه'],
            ['علامت' => 'خطاهای ECU یا چراغ هشدار', 'علت' => 'سنسور یا ماژول معیوب', 'اقدام' => 'دیاگ و بررسی ماژول/سنسور']
        ];

        foreach ($rows as $r) {
            $html .= '<tr><td>' . htmlspecialchars($r['علامت']) . '</td><td>' . htmlspecialchars($r['علت']) . '</td><td>' . htmlspecialchars($r['اقدام']) . '</td></tr>';
        }

        $html .= '</tbody></table></div>';
        $html .= '<h3>نکات نگهداری مرتبط</h3><div class="section-body"><p>تعویض منظم فیلترها، بررسی سطح روغن و سرویس دوره‌ای به حفظ عملکرد کمک می‌کند.</p></div>';

        while (str_word_count(strip_tags($html)) < $targetWords) {
            $html .= '<p>ادامهٔ نکات فنی و توضیحات تکمیلی برای پوشش کامل سناریوهای خطا و راه‌حل‌های پیشنهادی تعمیرگاهی.</p>';
            if (str_word_count(strip_tags($html)) > $targetWords + 2000) break;
        }

        $html .= '</div>';
        return ['title' => $title, 'html' => $html];
    }
}
