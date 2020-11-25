<?php

namespace RyanKikta;

final class API
{

    const GATEWAY = 'https://api.ryankikta.com/api.php';

    public static $timeout = 3000;
    public static $key;
    public static $hash;

    private function __construct()
    {
        throw new \Exception('Cannot instantiate static class.');
    }

    public static function __callStatic($method, array $args)
    {
        if (!isset($args[0])) {
            $args[0] = [];
        }
        assert(is_array($args[0]));
        return self::call($method, $args[0]);
    }

    public static function call($method, array $data = [])
    {
        assert(isset(self::$key));
        assert(isset(self::$hash));
        $data['method'] = $method;
        $data['key'] = self::$key;
        $data['hash'] = self::$hash;
        $log = fopen(__DIR__ . '/log/' . REQUEST_ID, 'a');
        fputs($log, print_r($data, 1));
        fputs($log, "\n\n");
        fclose($log);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::GATEWAY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1); // this is required.
        //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true); // FIXME when API is HTTPS
        //curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,true); // FIXME when API is HTTPS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $responseText = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            error_log($responseText);
            throw new \Exception(curl_error($ch), $errno);
        }
        $response = json_decode($responseText);

        if (!is_object($response)) {
            error_log($method . ' -> ' . json_encode($data));
            error_log(json_last_error() . ' <- ' . $responseText);
            throw new \Exception('API response was not understood.', -1);
        }
        if (isset($response->message)) { // FIXME result code is not consistent!
            throw new Error($response->message);
        }

        return $response->results;
    }

    public static function listmyimages()
    {
        return self::call('listmyimages'); // FIXME does this only work SOMETIMES?
    }

    public static function listproducts()
    {
        static $products;
        if (!isset($products)) {
            $products = [];
            $allcolors = self::listcolors();
            $allsizes = self::listsizes();
            $allbrands = self::listbrands();
            foreach (self::call('listproducts') as $product) {
                $product->sizes = [];
                // build sizes => colors
                $product->colors = get_object_vars($product->colors);
                foreach ($product->colors as $color => $sizes) {
                    foreach ($sizes as $size) {
                        $product->sizes[$size][$color] = $allcolors[$color];
                    }
                }
                // sort sizes by name
                uksort($product->sizes, function ($a, $b) use (&$allsizes) {
                    if ($allsizes[$a]->size_group !== $allsizes[$b]->size_group) {
                        return strcmp($allsizes[$a]->size_group, $allsizes[$b]->size_group);
                    } else return strcmp($allsizes[$a]->size_name, $allsizes[$b]->size_name);
                });
                // sort colors
                foreach (array_keys($product->sizes) as $size) {
                    usort($product->sizes[$size], function ($a, $b) {
                        return strcmp($a->color_name, $b->color_name);
                    });
                }
                // include brand object
                $product->brand = $allbrands[$product->brand_id];
                $products[$product->product_id] = $product;
            }
            uasort($products, function ($a, $b) use (&$allbrands) {
                // sort by brand names first
                if ($a->brand_id != $b->brand_id) {
                    return strcmp($allbrands[$a->brand_id]->brand_name, $allbrands[$b->brand_id]->brand_name);
                }
                // sort by product name
                return strcmp($a->product_name, $b->product_name);
            });
        }
        return $products;
    }

    public static function listcolors()
    {
        static $colors;
        if (!isset($colors)) {
            foreach (self::call('listcolors') as $color) {
                // include brightness index 0-255
                // http://www.w3.org/TR/AERT
                $r = hexdec(substr($color->color_code, 0, 2));
                $g = hexdec(substr($color->color_code, 2, 2));
                $b = hexdec(substr($color->color_code, 4, 2));
                $color->brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                $colors[$color->color_id] = $color;
            }
            uasort($colors, function ($a, $b) {
                return strcmp($a->color_name, $b->color_name);
            });
        }
        return $colors;
    }

    public static function listsizes()
    {
        static $sizes;
        if (!isset($sizes)) {
            $sizes = [];
            foreach (self::call('listsizes') as $size) {
                if ($size->size_name = trim($size->size_name)) {
                    $sizes[$size->size_id] = $size;
                }
            }
            uasort($sizes, function ($a, $b) {
                return $a->size_group != $b->size_group ? strcmp($a->size_group, $b->size_group) : strcmp($a->size_name, $b->size_name);
            });
        }
        return $sizes;
    }

    public static function listbrands()
    {
        static $brands;
        if (!isset($brands)) {
            foreach (self::call('listbrands') as $brand) {
                $brands[$brand->brand_id] = $brand;
            }
            uasort($brands, function ($a, $b) {
                return strcmp($a->brand_name, $b->brand_name);
            });
        }
        return $brands;
    }

    public static function viewproducts()
    {
        // FIXME API should already do this and not return an error
        try {
            return self::call('viewproducts');
        } catch (Error $e) {
            if (strpos($e->getMessage(), 'You currently have no RyanKikta Products') === 0) {
                return [];
            } else throw $e;
        }
    }

    public static function viewapps()
    {
        static $apps;
        if (!isset($apps)) {
            $apps = get_object_vars(self::call('viewapps')->apps);
        }
        return $apps;
    }

}

API::$key = $_SESSION['key'];
API::$hash = $_SESSION['hash'];
