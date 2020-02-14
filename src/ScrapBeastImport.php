<?php
class ScrapBeastImport
{
    public $sourceUrl;

    public function setSourceUrl($url)
    {
        $this->sourceUrl = $url;
    }

    public function start()
    {
        $products = $this->_getSourceProducts();
        if (empty($products)) {
            return;
        }

        foreach ($products as $product) {
            $this->_saveProductToDatabase($product);
        }
    }

    private function _saveProductToDatabase($product) {

        $urlManager = new \Microweber\Providers\UrlManager();
        $productUrl = $urlManager->slug($product['title']);

        $productDbId = false;
        $findProduct = get_content('single=1&url=' . $productUrl);
        if ($findProduct) {
            $productDbId = $findProduct['id'];
        }

        $readyContent = array();
        if ($productDbId) {
            $readyContent['id'] = $productDbId;
        } else {
            // Download images
        }

        // Download images
        /*if (!is_dir( media_uploads_path() .'scrapbeast')) {
            mkdir_recursive( media_uploads_path() .'scrapbeast');
        }*/

        $downloadedImages = [];
        foreach ($product['images'] as $image) {
            $targetImageFile = media_uploads_path() . md5($image['url']) . '.jpg';
            if (is_file($targetImageFile)) {
                $downloadedImages[] = $targetImageFile;
                continue;
            }
            $imageContent = file_get_contents($image['url']);
            if ($imageContent) {
                $saveImage = file_put_contents($targetImageFile, $imageContent);
                if ($saveImage) {
                    $downloadedImages[] = media_uploads_url() . md5($image['url']) . '.jpg';
                }
            }
        }

        if (!empty($downloadedImages)) {
            $images = implode(', ', $downloadedImages);
            $readyContent['images'] = $images;
        }

        $readyContent['title'] = $product['title'];

        $readyContent['content'] = '';
        $readyContent['content_type'] = 'product';
        $readyContent['subtype'] = 'product';
        $readyContent['is_active'] = 1;

        $readyContent['url'] = $productUrl;

       //$tags = implode(', ', $tags);

        //$readyContent['tags'] = $tags;

        // $categories = implode(', ', $categories);
        //   $readyContent['categories'] = $categories;

        $readyContent['custom_field_price'] = $product['price'];

        if ($product['stock']) {
            $readyContent['data_qty'] = 3;
        } else {
            $readyContent['data_qty'] = 0;
        }

        $readyContent['data_sku'] = $product['remote_id'];
        $readyContent['custom_fields'] = array(
            //  array('type' => 'dropdown', 'name' => 'Color', 'value' => array('Purple', 'Blue')),
        );

        $save = save_content($readyContent);

        clearcache();
    }

    private function _getSourceProducts()
    {
        $data = file_get_contents($this->sourceUrl);
        return json_decode($data, true);
    }
}

