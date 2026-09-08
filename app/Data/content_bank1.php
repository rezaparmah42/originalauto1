<?php
// content bank 1 (service-first) — condensed excerpts mapped by service slug
return [
    'diagnostic' => [
        'why' => 'هرچه خودرو الکترونیکی‌تر باشد، دیاگ حیاتی‌تر است. در بسیاری از موارد، خطاهای ثبت‌شده در ECU یا ماژول‌ها تنها با دیاگ تخصصی قابل تفسیر و رفع ریشه‌ای هستند.',
        'vehicle_targets' => [
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'اسکن کامل سیستم‌ها'],
            ['slug'=>'cerato','label'=>'کیا سراتو','common_subservice'=>'بررسی ماژول‌ها و BSI'],
            ['slug'=>'elantra','label'=>'هیوندای النترا','common_subservice'=>'دیاگ موتور و شناسایی صدا']
        ]
    ],
    'electrical' => [
        'why' => 'برق خودرو یکی از پرتکرارترین دلایل مراجعه است؛ سیم‌کشی فرسوده، رله‌ها و دینام می‌تواند باعث خاموشی ناگهانی یا مشکلات متعدد الکتریکی شود.',
        'vehicle_targets' => [
            ['slug'=>'pride','label'=>'پراید','common_subservice'=>'تعمیر رله و سیم‌کشی'],
            ['slug'=>'tiba','label'=>'تیبا','common_subservice'=>'تست باتری و کابل‌ها'],
            ['slug'=>'samand','label'=>'سمند','common_subservice'=>'بازرسی جعبه فیوز و اتصالات']
        ]
    ],
    'engine' => [
        'why' => 'موتورها بسته به طراحی‌شان حساسیت‌های متفاوت دارند؛ برخی به خنک‌کاری، برخی به روغن و برخی به کیفیت سوخت حساس‌اند و تشخیص زودهنگام از خرابی سنگین جلوگیری می‌کند.',
        'vehicle_targets' => [
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'بازرسی واشر سرسیلندر'],
            ['slug'=>'samand','label'=>'سمند (XU7)','common_subservice'=>'سرویس خنک‌کاری و هواگیری'],
            ['slug'=>'elantra','label'=>'النترا','common_subservice'=>'بررسی مصرف روغن و صدا']
        ]
    ],
    'gearbox' => [
        'why' => 'گیربکس‌های اتوماتیک به روغن و شیربرقی حساس‌اند؛ تشخیص زودهنگام با دیاگ و بررسی فشار روغن می‌تواند از آسیب‌های گسترده جلوگیری کند.',
        'vehicle_targets' => [
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶ اتومات (AL4)','common_subservice'=>'بررسی شیربرقی و فشار روغن'],
            ['slug'=>'cerato','label'=>'کیا سراتو','common_subservice'=>'آزمایش و تعویض روغن ATF'],
            ['slug'=>'tiggo-5','label'=>'تیگو ۵ (CVT)','common_subservice'=>'سرویس CVT با دستگاه']
        ]
    ],
    'periodic-service' => [
        'why' => 'سرویس دوره‌ای پایهٔ سلامت خودرو است و در خودروهای پرکارکرد یا توربو اهمیت بیشتری دارد؛ تعویض منظم روغن و فیلترها عملکرد و دوام را تضمین می‌کند.',
        'vehicle_targets' => [
            ['slug'=>'pride','label'=>'پراید','common_subservice'=>'تعویض روغن و فیلترها'],
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'سرویس کامل قبل از سفر'],
            ['slug'=>'tiggo-7','label'=>'تیگو ۷','common_subservice'=>'سرویس موتور و گیربکس']
        ]
    ],
    'ac-repair' => [
        'why' => 'در اقلیم گرم، کولر یکی از نخستین نیازهاست؛ خرابی رلهٔ فن، کمپرسور یا نشتی گاز باعث افت محسوس سرمایش می‌شود.',
        'vehicle_targets' => [
            ['slug'=>'samand','label'=>'سمند','common_subservice'=>'شارژ گاز و تعمیر کمپرسور'],
            ['slug'=>'cerato','label'=>'کیا سراتو','common_subservice'=>'رفع بوی بد و شارژ گاز'],
            ['slug'=>'tondar-90','label'=>'تندر ۹۰','common_subservice'=>'نشتی‌یابی کندانسور']
        ]
    ],
    'suspension' => [
        'why' => 'جاده‌های ناهموار باعث فرسایش زودهنگام بوش، طبق و کمک‌فنر می‌شوند؛ بررسی و تنظیم زوایا برای حفظ هندلینگ و ایمنی ضروری است.',
        'vehicle_targets' => [
            ['slug'=>'cerato','label'=>'کیا سراتو','common_subservice'=>'تعویض بوش طبق'],
            ['slug'=>'tiggo-8','label'=>'تیگو ۸','common_subservice'=>'بازدید کمک‌فنر و تعلیق'],
            ['slug'=>'x33','label'=>'ام‌وی‌ام X33','common_subservice'=>'تعویض سیبک و طبق']
        ]
    ],
    'brakes' => [
        'why' => 'ترمز یکی از حیاتی‌ترین سیستم‌هاست؛ گود شدن دیسک، سوت لنت یا خطاهای ABS باید سریع بررسی شوند تا ایمنی حفظ شود.',
        'vehicle_targets' => [
            ['slug'=>'tondar-90','label'=>'تندر ۹۰','common_subservice'=>'بررسی دیسک و لنت'],
            ['slug'=>'tiggo-8','label'=>'تیگو ۸','common_subservice'=>'تعویض دیسک و سرویس ABS'],
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'سرویس کالیپر']
        ]
    ],
    'battery' => [
        'why' => 'باتری و دینام کارکرد خودرو را تعیین می‌کنند؛ تست و عیب‌یابی سیستم شارژ از مراجعه‌های پرتکرار به تعمیرگاه است.',
        'vehicle_targets' => [
            ['slug'=>'pride','label'=>'پراید','common_subservice'=>'تست و تعویض باتری'],
            ['slug'=>'tiba','label'=>'تیبا','common_subservice'=>'عیب‌یابی دینام'],
            ['slug'=>'tiggo-7','label'=>'تیگو ۷','common_subservice'=>'بررسی BCM و برق‌دزدی']
        ]
    ],
    'ecu' => [
        'why' => 'ECU و بردهای الکترونیکی حساس‌اند؛ بردهای آسیب‌دیده یا خطاهای نرم‌افزاری نیاز به تعمیر یا ریمپ تخصصی دارند.',
        'vehicle_targets' => [
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'تعمیر برد ECU'],
            ['slug'=>'samand','label'=>'سمند','common_subservice'=>'دیاگ و پاک‌سازی خطا'],
            ['slug'=>'tiggo-7','label'=>'تیگو ۷','common_subservice'=>'ریمپ و کالیبراسیون']
        ]
    ],
    'pre-purchase-inspection' => [
        'why' => 'بازرسی پیش از خرید ریسک معامله را کاهش می‌دهد؛ خصوصاً برای مدل‌های پرتقلب یا وارداتی که نیاز به بررسی اصالت و شاسی دارند.',
        'vehicle_targets' => [
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'کارشناسی بدنه و موتور'],
            ['slug'=>'cerato','label'=>'کیا سراتو','common_subservice'=>'بازرسی گیربکس و تست جاده‌ای'],
            ['slug'=>'camry','label'=>'تویوتا کمری','common_subservice'=>'بازرسی کامل فنی']
        ]
    ],
    'emergency' => [
        'why' => 'خدمات امداد در محل برای روشن‌کردن باتری، تعویض باتری و رفع نقایص موقت ضروری است و اغلب زندگی روزمره را نجات می‌دهد.',
        'vehicle_targets' => [
            ['slug'=>'pride','label'=>'پراید','common_subservice'=>'روشن‌کردن باتری در محل'],
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'عیب‌یابی فوری'],
            ['slug'=>'samand','label'=>'سمند','common_subservice'=>'تعویض باتری سیار']
        ]
    ],
    'airbag' => [
        'why' => 'سیستم ایمنی بعد از هر تصادف نیاز به بازبینی دارد؛ چراغ ایربگ یا خطای سنسورها باید با دیاگ و بررسی سخت‌افزاری اصلاح شود.',
        'vehicle_targets' => [
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'رفع چراغ ایربگ و سنسورها'],
            ['slug'=>'cerato','label'=>'کیا سراتو','common_subservice'=>'تعویض سنسور ضربه'],
            ['slug'=>'tiggo-7','label'=>'تیگو ۷','common_subservice'=>'کالیبراسیون ایربگ']
        ]
    ],
    'car-air-filter-and-filters' => [
        'why' => 'فیلترها به‌ویژه در شرایط آلودگی و سوخت نامرغوب، عملکرد موتور و سیستم سوخت‌رسانی را تحت‌تأثیر قرار می‌دهند؛ نگهداری منظم آنها ضروری است.',
        'vehicle_targets' => [
            ['slug'=>'pride','label'=>'پراید','common_subservice'=>'تعویض فیلتر سوخت و هوا'],
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'فیلتر کابین و هوا'],
            ['slug'=>'tiggo-5','label'=>'تیگو ۵','common_subservice'=>'فیلتر CVT/هوا']
        ]
    ],
    'oil-change' => [
        'why' => 'تعویض روغن منظم و استفاده از روغن استاندارد پایهٔ نگهداری موتور است؛ مخصوصاً در موتورهای حساس به نوع روغن و خودروهای توربوشارژ.',
        'vehicle_targets' => [
            ['slug'=>'peugeot-206','label'=>'پژو ۲۰۶','common_subservice'=>'تعویض روغن با استاندارد توصیه‌شده'],
            ['slug'=>'pride','label'=>'پراید','common_subservice'=>'تعویض روغن و فیلتر'],
            ['slug'=>'tiggo-7','label'=>'تیگو ۷','common_subservice'=>'سرویس روغن گیربکس در صورت نیاز']
        ]
    ],
];
