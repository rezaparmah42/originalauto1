<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\KnowledgeContent;

class KnowledgeController extends Controller
{
    public function model(string $model)
    {
        $items = (new KnowledgeContent())->byModel($model);
        if (!$items) {
            http_response_code(404);
            $this->view('vehicles/not-found', ['title' => 'محتوای خودرو یافت نشد']);
            return;
        }

        $this->view('knowledge/model', ['model' => $model, 'items' => $items]);
    }

    public function byVehicle(string $brand, string $model)
    {
        $items = (new KnowledgeContent())->byVehicle($brand, $model);
        if (!$items) {
            http_response_code(404);
            $this->view('vehicles/not-found', ['title' => 'محتوای خودرو یافت نشد']);
            return;
        }

        $this->view('knowledge/model', ['model' => $model, 'brand' => $brand, 'items' => $items]);
    }

    public function show(string $slug, ?string $brand = null, ?string $model = null)
    {
        $item = (new KnowledgeContent())->findPublished($slug, $brand, $model);
        if (!$item) {
            http_response_code(404);
            $this->view('vehicles/not-found', ['title' => 'صفحه یافت نشد']);
            return;
        }

        $this->view('knowledge/show', ['item' => $item]);
    }
}

