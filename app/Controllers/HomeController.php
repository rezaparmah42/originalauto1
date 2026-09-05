<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Service;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $articleModel = new Article();
        $serviceModel = new Service();
        $productModel = new Product();

        $this->view('home/index', [
            'services' => $serviceModel->getVisibleServices(),
            'articles' => $articleModel->getVisibleArticles(),
            'products' => array_slice($productModel->getVisibleProducts(), 0, 4),
            'title' => 'تعمیرگاه تخصصی خودرو | اورجینال شرق',
            'description' => 'خدمات تخصصی دیاگ، برق، موتور، گیربکس و سرویس دوره‌ای در تعمیرگاه اورجینال شرق با تجهیزات حرفه‌ای و ضمانت کیفیت.',
            'canonical' => SITE_URL . '/',
            'robots' => 'index, follow',
        ]);
    }

    public function about()
    {
        $this->view('about/index', [
            'title' => 'درباره اورجینال شرق | ' . SITE_NAME,
            'description' => 'آشنایی با رویکرد فنی اورجینال شرق در عیب‌یابی، تعمیر و پشتیبانی خودرو.',
            'canonical' => SITE_URL . '/about',
            'robots' => 'index, follow',
        ]);
    }

    public function contact()
    {
        $this->view('contact/index', [
            'title' => 'تماس با اورجینال شرق | ' . SITE_NAME,
            'description' => 'راه‌های تماس با تعمیرگاه اورجینال شرق برای مشاوره، رزرو و پیگیری خدمات خودرو.',
            'canonical' => SITE_URL . '/contact',
            'robots' => 'index, follow',
        ]);
    }
}
